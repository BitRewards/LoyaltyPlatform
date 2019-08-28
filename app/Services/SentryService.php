<?php

namespace App\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SentryService
{
    const SENTRY_API_URL = 'https://sentry.io/api/0/';

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var string
     */
    protected $organization;

    /**
     * SentryService constructor.
     *
     * @param ClientInterface $client
     * @param string          $organizationSlug
     */
    public function __construct(ClientInterface $client, string $organizationSlug)
    {
        $this->client = $client;
        $this->organization = $organizationSlug;
    }

    /**
     * Returns current release version.
     *
     * @param string|null $project
     *
     * @return mixed
     */
    public static function currentVersion(string $project = null)
    {
        switch ($project) {
            case 'frontend':
                return Cache::get('sentry.frontend_release_version');

            case 'backend':
                return Cache::get('sentry.backend_release_version');

            default:
                return Cache::get('sentry.release_version');
        }
    }

    /**
     * Appends project path to API URL.
     *
     * @param string $project
     * @param string $path
     *
     * @return string
     */
    protected function projectUrl(string $project, string $path)
    {
        return static::SENTRY_API_URL.'/projects/'.$this->organization.'/'.$project.'/'.ltrim($path, '/');
    }

    /**
     * Creates new release for given project.
     *
     * @param string $project
     * @param string $version
     *
     * @return bool|string
     */
    public function release(string $project, string $version)
    {
        try {
            $this->client->request('POST', $this->projectUrl($project, '/releases/'), [
                'json' => ['version' => $version],
            ]);
        } catch (RequestException $e) {
            Log::debug('[SentryService@release] Got exception', [
                'message' => $e->getMessage(),
                'status_code' => $e->getResponse()->getStatusCode(),
            ]);

            return false;
        }

        return $version;
    }

    /**
     * Uploads artifact file for given project/release pair.
     *
     * @param string $project
     * @param string $version
     * @param string $path
     * @param string $name
     *
     * @return bool
     */
    public function uploadArtifact(string $project, string $version, string $path, string $name)
    {
        try {
            $this->client->request('POST', $this->projectUrl($project, '/releases/'.$version.'/files/'), [
                'multipart' => [
                    [
                        'name' => 'name',
                        'contents' => $name,
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($path, 'r'),
                        'filename' => pathinfo($name, PATHINFO_BASENAME),
                    ],
                ],
            ]);
        } catch (RequestException $e) {
            Log::debug('[SentryService@uploadArtifact] Got exception', [
                'message' => $e->getMessage(),
                'status_code' => $e->getResponse()->getStatusCode(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * Resets release for given project.
     *
     * @param string $project
     * @param string $version
     *
     * @return bool
     */
    public function clearRelease(string $project, string $version)
    {
        try {
            $this->client->request('DELETE', $this->projectUrl($project, '/releases/'.$version.'/'));
        } catch (RequestException $e) {
            return false;
        }

        return true;
    }
}
