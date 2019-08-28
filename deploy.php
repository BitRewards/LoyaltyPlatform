<?php

namespace Deployer;

require_once 'recipe/common.php';

function isProduction()
{
    return 'production' === input()->getArgument('stage');
}

function artisan($cmd, $outputType = 'info')
{
    return function () use ($cmd, $outputType) {
        $script = isProduction() ? 'artisan' : 'dartisan';
        $output = run("cd {{release_path}} && ./$script $cmd");
        writeln("<{$outputType}>{$output}</{$outputType}>");
    };
}

set('shared_dirs', [
    'storage',
    'vendor',
    'node_modules',
]);

//set('writable_dirs', [
//    'bootstrap/cache',
//    'storage',
//    'storage/app',
//    'storage/app/public',
//    'storage/framework',
//    'storage/framework/cache',
//    'storage/framework/sessions',
//    'storage/framework/views',
//    'storage/logs',
//]);

task('dartisan:up', artisan('up'))
    ->desc('Disable maintenance mode');

task('dartisan:down', artisan('down', 'error'))
    ->desc('Enable maintenance mode');

task('dartisan:migrate', artisan('migrate --force'))
    ->desc('Execute artisan migrate');

task('dartisan:migrate:rollback', artisan('migrate:rollback --force'))
    ->desc('Execute artisan migrate:rollback');

task('dartisan:migrate:status', artisan('migrate:status'))
    ->desc('Execute artisan migrate:status');

task('dartisan:db:seed', artisan('db:seed --force'))
    ->desc('Execute artisan db:seed');

task('dartisan:cache:clear', artisan('cache:clear'))
    ->desc('Execute artisan cache:clear');

task('dartisan:config:cache', artisan('config:cache'))
    ->desc('Execute artisan config:cache');

task('dartisan:view:clear', artisan('view:clear'))
    ->desc('Execute artisan view:clear');

task('dartisan:queue:restart', artisan('queue:restart'))
    ->desc('Execute dartisan queue:restart');

task('dartisan:route:cache', artisan('route:cache'))
    ->desc('Execute dartisan route:cache');

task('docker:permissions', function () {
    run('chmod +x {{release_path}}/docker-*');
    run('chmod +x {{release_path}}/docker-exec-no-tty');
    run('chmod +x {{release_path}}/dartisan');
    run('chmod +x {{release_path}}/phpunit');
})
    ->onStage('testing_stage')
    ->desc('Making scripts executable');

task('docker:configure', function () {
    run('cd {{release_path}} && ln -sf docker-compose.dist.yml docker-compose.yml');
    run('cd {{release_path}} && ln -sf stage.override.dist.yml docker-compose.override.yml');
    run('cd {{release_path}} && echo "LOCAL_GIFTD_API=$LOCAL_GIFTD_API" >> .env');
})
    ->onStage('testing_stage')
    ->desc('Copy docker configs');

task('docker:rebuild', function () {
    $output = run('cd {{release_path}} && docker-compose build');
    writeln('<info>'.$output.'</info>');
})
    ->onStage('testing_stage')
    ->desc('Execute docker-rebuild');

task('docker:restart', function () {
    try {
        $output = run('test -d {{current_path}} && cd {{current_path}} && docker-compose down');
        writeln('<info>'.$output.'</info>');
    } catch (\Exception $e) {
    }

    $output = run('cd {{release_path}} && docker-compose up -d');
    writeln('<info>'.$output.'</info>');
})
    ->onStage('testing_stage')
    ->desc('Execute docker-rebuild');

task('docker:npm', function () {
    run('ln -sf {{release_path}}/package.json /crm/shared/package.json');

    if (isProduction()) {
        $output = run('cd {{release_path}} && npm i --prefix /crm/shared/ --no-audit');
    } else {
        $output = run('cd {{release_path}} && ./docker-gulp-npm');
    }

    writeln('<info>'.$output.'</info>');
})->desc('Execute docker-gulp');

task('docker:gulp', function () {
    if (isProduction()) {
        $gulp = 'cd /crm/shared && /crm/shared/node_modules/.bin/gulp --gulpfile {{release_path}}/gulpfile.js';
        $output = run("$gulp loyalty; $gulp --production");
    } else {
        $output = run('cd {{release_path}} && ./docker-gulp');
    }

    writeln('<info>'.$output.'</info>');
})->desc('Execute docker-gulp');

task('logs:remove_old', artisan('logs:removeOld'))
    ->desc('Remove old logs');

task('api:generate-docs', artisan('make:api-docs'))
    ->desc('Regenerate API docs');

task('composer:install', function () {
    $script = 'cd {{release_path}} && composer install  --no-interaction --no-suggest';

    if (!isProduction()) {
        $script = 'cd {{release_path}} && ./docker-exec-no-tty "composer install  --no-interaction --no-suggest"';
    }

    $output = run($script);
    writeln('<info>'.$output.'</info>');

    $script = 'cd {{release_path}} && cd vendor/giftd/library && git pull';
    $output = run($script);
    writeln('<info>'.$output.'</info>');
});

task('sentry:release', artisan('sentry:release'))
    ->desc('Create new release & send artifacts to Sentry');

task('supervisord:update-config', function () {
    run('envsubst < {{release_path}}/docker/supervisord/laravel-worker.conf > /etc/supervisor/conf.d/laravel-worker.conf');
})->onStage('production');

task('supervisord:reload', function () {
    run('supervisorctl reread');
    run('supervisorctl update');
    run('supervisorctl restart all');
})->onStage('production');

task('crontab:update', function () {
    run("sudo su root -c 'crontab -u www-data {{release_path}}/docker/crontab'");
})->onStage('production');

task('nginx:update-config', function () {
    run('cp {{release_path}}/docker/nginx/default.conf /etc/nginx/conf.d/default.conf');
    run("sed -i 's/listen 8891 default_server/listen 80 default_server/g' /etc/nginx/conf.d/default.conf");
})->onStage('production');

task('nginx:reload', function () {
    run('sudo nginx -s reload'); // It's sudo use without password. See /etc/sudoers
})->onStage('production');

task('mark:stage', function () {
    $stage = input()->getArgument('stage');
    run("echo $stage > {{release_path}}/.stage");
})->onStage('testing_stage');

task('announce', function () {
    $branch = get('branch');
    writeln("<info>Deploying '$branch' branch...</info>");
});

task('storage:make', function () {
    run('mkdir -p {{release_path}}/bootstrap/cache');
    run('chmod 0777 {{release_path}}/bootstrap/cache');
    run('mkdir -p {{release_path}}/storage/app/public');
    run('mkdir -p {{release_path}}/storage/framework/cache');
    run('mkdir -p {{release_path}}/storage/framework/views');

    run('mkdir -p {{release_path}}/public/build');
    run('chmod -R 0777 {{release_path}}/public/build');

    run('chmod -R 0777 {{release_path}}/storage');

    run('rm -rf {{release_path}}/storage/logs && ln -sfn {{deploy_path}}/shared/storage/logs {{release_path}}/storage/logs');
    run('ln -sfn {{deploy_path}}/shared/node_modules {{release_path}}/node_modules');
})->desc('Make storage');

task('deploy', [
    'announce',
    'deploy:prepare',
    'deploy:release',
    'deploy:update_code',
    'storage:make',
    'deploy:shared',
    'deploy:writable',
    'docker:permissions',
    'docker:configure',
    'docker:rebuild',
    'docker:restart',
    'composer:install',
    'docker:npm',
    'docker:gulp',
    'deploy:symlink',
    'dartisan:migrate',
    'dartisan:cache:clear',
    'dartisan:config:cache',
    'dartisan:queue:restart',
    'api:generate-docs',
    'sentry:release',
    'logs:remove_old',
    'supervisord:update-config',
    'supervisord:reload',
    'crontab:update',
    'nginx:update-config',
    'nginx:reload',
    'mark:stage',
    'cleanup',
])->desc('Deploying crm');

after('deploy', 'success');

host('alpha')
    ->user('root')
    ->hostname('**REMOVED**')
    ->stage('testing_stage')
    ->set('deploy_path', '**REMOVED**')
    ->set('env', [
        'APP_ENV' => 'testing',
        'ENV' => 'testing',
        'LOCAL_GIFTD_API' => '**REMOVED**',
    ]);

host('beta')
    ->user('root')
    ->hostname('**REMOVED**')
    ->stage('testing_stage')
    ->set('deploy_path', '**REMOVED**')
    ->set('env', [
        'APP_ENV' => 'testing',
        'ENV' => 'testing',
        'LOCAL_GIFTD_API' => '**REMOVED**',
    ]);

host('gamma')
    ->user('root')
    ->hostname('**REMOVED**')
    ->stage('testing_stage')
    ->set('deploy_path', '**REMOVED**')
    ->set('env', [
        'APP_ENV' => 'testing',
        'ENV' => 'testing',
        'LOCAL_GIFTD_API' => '**REMOVED**',
    ]);

host('delta')
    ->user('root')
    ->hostname('**REMOVED**')
    ->stage('testing_stage')
    ->set('deploy_path', '**REMOVED**')
    ->set('env', [
        'APP_ENV' => 'testing',
        'ENV' => 'testing',
        'LOCAL_GIFTD_API' => '**REMOVED**',
    ]);

host('**REMOVED**')
    ->user('deployer')
    ->stage('production')
    ->set('deploy_path', '**REMOVED**');

set('repository', '**REMOVED**');
set('default_timeout', 3600);
set('use_relative_symlink', false);
set('writable_mode', 'chmod');
set('writable_chmod_mode', '0777');
set('branch', function () {
    if (isProduction()) {
        return 'master';
    }

    try {
        $branch = runLocally('git rev-parse --abbrev-ref HEAD');
    } catch (\Throwable $exception) {
        $branch = null;
    }

    if ('HEAD' === $branch) {
        $branch = null; // Travis-CI fix
    }

    if (input()->hasOption('branch') && !empty(input()->getOption('branch'))) {
        $branch = input()->getOption('branch');
    }

    return $branch;
});
