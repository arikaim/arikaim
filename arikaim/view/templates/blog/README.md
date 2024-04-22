## Arikaim CMS Blog template
![version: 1.0.0](https://img.shields.io/github/release/arikaim/blog-template.svg)
![license: GPL3](https://img.shields.io/badge/License-GPLv3-blue.svg)


### Requirements 
  * [Arikaim CMS](https://github.com/arikaim/arikaim)
  * [Blog Extension](https://github.com/arikaim/blog-extension)
  * UI libraries
    * jquery
    * arikaim
    * arikaim-ui
    * tailwind


### Installation

```sh
composer require arikaim/blog-template
```

### Customization 

In theme folder: 

1. Install Tailwind CSS
```sh
    npm install -D tailwindcss@latest postcss@latest autoprefixer@latest
```
2.  Install daisyui
```sh
    npm i daisyui
```

3. Start watch process
```sh
    npx tailwindcss -o css/tailwind.min.css -m --watch
```