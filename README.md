<div align="center">
    <br>
    <img alt="charcoal" src="charcoal-logo.png"/>
    <br>
    <br>
    <h1>Charcoal Framework - Website fuel</h1>
    <small>A master repo hosting the totality of Charcoal Core packages</small>
</div>

[![License][badge-license]][charcoal]
[![Latest Stable Version][badge-version]][charcoal]
[![semantic-release: angular](https://img.shields.io/badge/semantic--release-angular-e10079?logo=semantic-release)](https://github.com/semantic-release/semantic-release)
[![Commitizen friendly](https://img.shields.io/badge/commitizen-friendly-brightgreen.svg)](http://commitizen.github.io/cz-cli/)
[![Php version][badge-php]][charcoal]


This monorepo contains the integrality of the Charcoal Framework that can be used directly within a website project.
You'll find all the different packages in [`/packages`](./packages/) directory. These packages all also individually hosted in `READONLY` format under the [charcoal][charcoal-git].

## Charcoal packages

| Package                                                                             | Description |
|-------------------------------------------------------------------------------------|-------------|
| [`charcoal-admin`](https://github.com/locomotive-charcoal/charcoal-admin)           |             |
| [`charcoal-app`](https://github.com/locomotive-charcoal/charcoal-app)               |             |
| [`charcoal-attachment`](https://github.com/locomotive-charcoal/charcoal-attachment) |             |
| [`charcoal-cache`](https://github.com/locomotive-charcoal/charcoal-cache)           |             |
| [`charcoal-cms`](https://github.com/locomotive-charcoal/charcoal-cms)               |             |
| [`charcoal-config`](https://github.com/locomotive-charcoal/charcoal-config)         |             |
| [`charcoal-core`](https://github.com/locomotive-charcoal/charcoal-core)             |             |
| [`charcoal-email`](https://github.com/locomotive-charcoal/charcoal-email)           |             |
| [`charcoal-factory`](https://github.com/locomotive-charcoal/charcoal-factory)       |             |
| [`charcoal-image`](https://github.com/locomotive-charcoal/charcoal-image)           |             |
| [`charcoal-object`](https://github.com/locomotive-charcoal/charcoal-object)         |             |
| [`charcoal-property`](https://github.com/locomotive-charcoal/charcoal-property)     |             |
| [`charcoal-queue`](https://github.com/locomotive-charcoal/charcoal-queue)           |             |
| [`charcoal-translator`](https://github.com/locomotive-charcoal/charcoal-translator) |             |
| [`charcoal-ui`](https://github.com/locomotive-charcoal/charcoal-ui)                 |             |
| [`charcoal-user`](https://github.com/locomotive-charcoal/charcoal-user)             |             |
| [`charcoal-view`](https://github.com/locomotive-charcoal/charcoal-view)             |             |


## Installation

The preferred (and only supported) method is with Composer:

```shell
$ composer require locomotive-charcoal/charcoal
```
> Note that `charcoal` is intended to be run along a `charcoal-app` based project. To start from a boilerplate:
>
> ```shell
> $ composer create-project locomotive-charcoal/boilerplate

### À la carte methode

If possible, allow custom composer require. (TODO)

### Dependencies

#### Required

- [**PHP ^7.4**](https://php.net) || [**PHP ^8.0**](https://php.net)

## Configuration

## Usage

## Development

Development is made in a seperate branch from the ``main`` branch. 

To install the development environment:

```shell
$ composer install
```

To run the scripts (phplint, phpcs, and phpunit):

```shell
$ composer test
```

### Maintenance and Automations

https://github.com/symplify/monorepo-builder monorepo-builder is used to handle the conformity between the core repo and it's packages. It will sync composer.json files and packages versions.

Semantic release config in .releaserc

### Development Dependencies

- [symplify/monorepo-builder](https://github.com/symplify/monorepo-builder)

### Development History

This monorepo was created with a many to mono aproach using this guide and tool :

[hraban/tomono](https://github.com/hraban/tomono)


### Github Actions

| Actions                                                      | Description                                                                                                                                                                                                        |
|--------------------------------------------------------------|--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| [Split&nbsp;Monorepo](.github/workflows/split_monorepo.yaml) | The split action splits the packages into individual repositories. Only triggered when a tag is pushed. Based on [symplify/monorepo-split-github-action](https://github.com/symplify/monorepo-split-github-action) |
| [Release](.github/workflows/release.yaml)                    | (https://github.com/marketplace/actions/action-for-semantic-release) <br/> https://github.com/bahmutov/npm-install                                                                                                 |

## Credits

- [Locomotive](https://locomotive.ca/)
- [Joel Alphonso](mailto:joel@locomotive.ca)


## Contributors

[![contributors](https://contrib.rocks/image?repo=Locomotive-Charcoal/charcoal)](https://github.com/Locomotive-Charcoal/charcoal/graphs/contributors)

Made with [contrib.rocks](https://contrib.rocks).

## Changelog

View [CHANGELOG](docs/CHANGELOG.md).

## License

Charcoal is licensed under the MIT license. See [LICENSE](LICENSE) for details.

[charcoal]:         https://packagist.org/packages/locomotive-charcoal/charcoal
[charcoal-git]:     https://github.com/locomotive-charcoal

[badge-license]:      https://img.shields.io/packagist/l/locomotive-charcoal/charcoal.svg?style=flat-square
[badge-version]:      https://img.shields.io/packagist/v/locomotive-charcoal/charcoal.svg?style=flat-square
[badge-scrutinizer]:  https://img.shields.io/scrutinizer/g/locomotive-charcoal/charcoal?style=flat-square
[badge-coveralls]:    https://img.shields.io/coveralls/locomotive-charcoal/charcoal?style=flat-square
[badge-travis]:       https://img.shields.io/travis/com/locomotive-charcoal/charcoal?style=flat-square
[badge-php]:          https://img.shields.io/packagist/php-v/locomotive-charcoal/charcoal?style=flat-square
[badge-tabulator]:    https://img.shields.io/github/package-json/dependency-version/locomotive-charcoal/charcoal/tabulator-tables?style=flat-square

[psr-1]:  https://www.php-fig.org/psr/psr-1/
[psr-2]:  https://www.php-fig.org/psr/psr-2/
[psr-3]:  https://www.php-fig.org/psr/psr-3/
[psr-4]:  https://www.php-fig.org/psr/psr-4/
[psr-6]:  https://www.php-fig.org/psr/psr-6/
[psr-7]:  https://www.php-fig.org/psr/psr-7/
[psr-11]: https://www.php-fig.org/psr/psr-11/
[psr-12]: https://www.php-fig.org/psr/psr-12/
