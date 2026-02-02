# Audit Ignore

`composer.json` has `audit.block-insecure: false` because all security advisories come from transitive dependencies of `sylius/sylius` in `require-dev`. They do not affect production usage of this library.
