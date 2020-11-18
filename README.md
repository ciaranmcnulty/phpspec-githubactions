# PhpSpec Github Actions Extension

This extension enhances PhpSpec failure messages inside Github Actions so that they can be displayed inline on a Pull Request as annotations.

## Installation

`composer require --dev ciaranmcnulty/phpspec-githubactions`

Add the followig to your project's `phpspec.yaml`:

```yaml
extensions:
    Cjm\PhpSpec\GithubActionsExtension ~
```

## Usage

The extension autodetects it is in a GA environment, so no further action is needed
