const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './**/*.html',
        './themes/default.json',
        '../../components/tailwind/**/*.html',
        '../../components/tailwind/**/*.json'
    ], 
    theme: {
        screens: {
            'xs': '300px',
            ...defaultTheme.screens,
        },
        extend: {},
    },
    variants: {
        extend: {
            backgroundColor: ({ after }) => after(['disabled'])
        },
    },
    darkMode: 'class',
    plugins: [        
        require("daisyui")
    ],
    daisyui: {
        styled: true,
        themes: true,
        base: true,
        utils: true,
        logs: true,
        rtl: false,
        prefix: "",
        darkTheme: "dark",
    },
    corePlugins: {    
        preflight: false  
    }
}
