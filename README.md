# Product manager

This is a fairly basic demonstration of what you can do with the [Firefly III](https://github.com/firefly-iii/firefly-iii/) **[API](https://api-docs.firefly-iii.org/)**.

## Introduction

This is a web-app, built on Laravel. It requires PHP 8.0. It can help you get an overview of the costs associated with a specific thing, such as your car, your pets or hobbies. It does this by collecting the tags associated with these things and group them according to the type of expense.

### Screenshots

![Screenshot](./docs/example2.png)

![Screenshot](./docs/example1.png)

## How it works

In your Firefly III installation, tag every product related expense with the `product-SUBJECT-TYPE` tag. For `SUBJECT`, you fill in the thing you wish to track. Use "car" for example. For `TYPE`, you fill in whatever the kind of cost is. Here are some examples:

* `product-car-initial-purchase`
* `product-car-repairs`
* `product-car-maintenance`
* `product-car-gas`

Or maybe:

* `product-washing-machine-initial-purchase`
* `product-washing-machine-soap`

The tool will give you an overview of each subject: for `initial-purchase`, `repairs` and the others the expenses will be listed. Also present is the amount spent per month.

### Initial purchase

Make sure you tag one withdrawal with `product-SUBJECT-initial-purchase`.

## Installation

Download or clone the source, run `composer install`. Copy `.env.example` to `.env` and fill in the APP_KEY (32 chars) and Firefly III API data. Visit your web browser. 

<!-- HELP TEXT -->
## Need help?

If you need support using Firefly III or the associated tools, come find us!

- [GitHub Discussions for questions and support](https://github.com/firefly-iii/firefly-iii/discussions/)
- [Gitter.im for a good chat and a quick answer](https://gitter.im/firefly-iii/firefly-iii)
- [GitHub Issues for bugs and issues](https://github.com/firefly-iii/firefly-iii/issues)
- [Follow me around for news and updates on Mastodon](https://fosstodon.org/@ff3)

<!-- END OF HELP TEXT -->

<!-- SPONSOR TEXT -->
## Donate

If you feel Firefly III made your life better, consider contributing as a sponsor. Please check out my [Patreon](https://www.patreon.com/jc5) and [GitHub Sponsors](https://github.com/sponsors/JC5) page for more information. Thank you for considering.


<!-- END OF SPONSOR -->



## Questions and support.

This tool is built by [James Cole](james@firefly-iii.org). Contact me for questions.

Some disclaimers:

- Its not multi currency and will always display EUR.

License is AGPL-3-or higher.
