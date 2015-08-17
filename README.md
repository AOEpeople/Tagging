# Tagging 

This PHP library provide the functionality to automatic create a new tag/version in a remote VCS repository dependending on the last created tag/version. It follows the semantic versioning pattern: http://semver.org/
You are be able configure the version type to increase. For example "major" or "minor" increasement.

Features:
 - Follows the semantic versioning pattern
 - Major, minor and patch version are supported
 - GIT integration (SVN currently not implemented, but will be come shortly)
 - Commit (and push) additional files before creating a new tag/version
 - Dry run. Just evaluate the next tag/version

## Index
- [Installation](#installation)
  - [Clone](#clone)
  - [Composer](#composer)
- [Usage](#usage)
  - [Version-Type](#version-type)
  - [Commit files before creating a new tag/version](#commit-files-before-creating-a-new-tagversion)
  - [Evaluate (dry run)](#evaluate-dry-run)

## Installation
This library is build with composer. So you can even clone this repisory and install the dependencies via composer or just require this library in the composer.json of your project.

### Clone
clone the repitory from GitHub

    git clone https://github.com/AOEpeople/Tagging.git .

and install dependencies via composer

    wget http://getcomposer.org/composer.phar
    php composer.phar install

### Composer
or just add the package name to your composer.json

    "require": {
        "aoe/tagging": "0.1.*"
    }
    
## Usage

    php bin/tagging [VCS] [REPOSITORY] [PATH]

for example:

    php bin/tagging git https://github.com/company/package.git /path/to/local/checkout
    
### Version-Type
By default the version will be increased by the "patch" version. For example: 0.2.5 will be increased to 0.2.6.
You can configure which version type you want to increase by setting the `--version-type` option

    php bin/tagging git https://github.com/company/package.git /path/to/local/checkout --version-type=minor
    
In the example above the minor version will be increased. For example: 0.2.5 will be increased tp 0.3.0.

Allowed version types are:
 - major
 - minor
 - patch

### Commit files before creating a new tag/version
In some cases it is useful to commit (and push) a file into the remote before a new tag/version will be created.
For example if you have a version file within your repository that must be updated before creating a new tag/version.
If you need to commit (and push) files into the remote, just add the `--commit-and-push` option. The value is required and must contain the relative path to the file that should be commited.

    php bin/tagging git https://github.com/company/package.git /path/to/local/checkout --commit-and-push=version.txt

The above example will commit (and push) the file "version.txt" into the remote before creating a new tag/version.

If you want to customize the message vor the commit, just append the `--message` option and specify the message as value.
By default the message is empty.

    php bin/tagging git https://github.com/company/package.git /path/to/local/checkout --commit-and-push=version.txt --message="my custom commit message"

### Evaluate (dry run)
You can set the `--evaluate` option to find out which tag/version will be created. If set, nothing is really done. You just get an output of the next version/tag that would be created without the evaluate option.

    php bin/tagging git https://github.com/company/package.git /path/to/local/checkout --evaluate
