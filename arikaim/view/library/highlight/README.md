## Arikaim CMS Highlight.js library
![version: 1.0.0](https://img.shields.io/github/release/arikaim/highlight-library.svg)


[Highlight.js](https://highlightjs.org/usage) library for Arikaim CMS 
[Prism](https://prismjs.com) library 


#### Requirements 
  * [Arikaim CMS](https://github.com/arikaim/arikaim)
  

#### Install

```sh

composer require arikaim/highlight-library

```

#### Include in theme 

In theme package file **arikaim-package.json**

add code

```json
"include": {    
    "library": [
        "highlight"
    ]
}
```

for prism library

```json
"include": {    
    "library": [
        "highlight:prism"
    ]
}
```
