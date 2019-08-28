# BitRewards Loyalty Platform

This software component is a loyalty platform with rewards in cryptocurrency based on ERC-20 token.

BitRewards Loyalty Platform allowing to perform these tasks:
* Reward users with BIT tokens for purchases;
* Reward users with BIT tokens for their friends purchases;
* Reward users with BIT tokens for sharing posts in Facebook, Twitter, Instagram and other social media;
* Reward users with BIT tokens for subscription on Facebook, Twitter, Instagram or there social account;
* Reward users with BIT tokens for reviews on any feedback service;
* Create your own action and reward users with BIT tokens;
* Redeem BIT tokens for discounts, items or special offers;
* Flexible reward with BIT tokens for customers’ or his or her friends’ purchases;
* Cumulative cashback with BIT tokens for customers’ or his or her friends’ purchases;
* Burn unused BIT tokens after a while.

This software can be adapted to work with any ERC-20 token. It is highly configurable and allows you to use it in a variety of scenarios.

## Installation

### Requirements

1. Docker
2. Docker-compose

### Running

1. Copy environment variables `cp .env.dist .env`
2. Copy docker-compose yaml file `cp docker-compose.dist.yml docker-compose.yml`
3. Set local ENV `export ENV=local`
4. Prepare permissions:
```
find storage/ bootstrap/cache/ -type d -exec chmod 777 {} \;
find storage/ bootstrap/cache/ -type f -exec chmod a+rw {} \;
```
5. Bring up containers `./docker-up`
6. Install dependencies `./docker-exec composer install`
7. Init DB `./bin/init-db`

## Testing

Run `./docker-phpunit`

## Misc

* List of artisan commands: `./dartisan list`
* Create you own admin: `./dartisan admin:make nickname@example.com 12345 --role=admin --lang=en`

## License

This project is licensed under the terms of the [MIT license](./LICENSE).
