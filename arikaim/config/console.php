<?php
/**
 * Console commands classes
 */
return [
    "Arikaim\\Core\\App\\Commands\\HelpCommand",   
    "Arikaim\\Core\\App\\Commands\\InstallCommand",
    "Arikaim\\Core\\App\\Commands\\ClearLogsCommand",
    "Arikaim\\Core\\App\\Commands\\AdminResetCommand",
    "Arikaim\\Core\\App\\Commands\\ComposerCommand",
    // update
    "Arikaim\\Core\\App\\Commands\\Update\\UpdateCommand",
    "Arikaim\\Core\\App\\Commands\\Update\\ControlPanelUpdateCommand",
    "Arikaim\\Core\\App\\Commands\\Update\\UiComponentsUpdateCommand",
    // cache
    "Arikaim\\Core\\App\\Commands\\Cache\\ClearCommand",
    "Arikaim\\Core\\App\\Commands\\Cache\\EnableCommand",
    "Arikaim\\Core\\App\\Commands\\Cache\\DisableCommand",
    // session
    "Arikaim\\Core\\App\\Commands\\Session\\RestartCommand",
    "Arikaim\\Core\\App\\Commands\\Session\\InfoCommand",
    // queue
    'Arikaim\\Core\\App\\Commands\\Queue\\JobsCommand',
    'Arikaim\\Core\\App\\Commands\\Queue\\WorkerCommand',
    'Arikaim\\Core\\App\\Commands\\Queue\\StopCommand',
    'Arikaim\\Core\\App\\Commands\\Queue\\CronCommand',
    // job
    'Arikaim\\Core\\App\\Commands\\Job\\RunJobCommand',
    // extensions
    "Arikaim\\Core\\App\\Commands\\Extensions\\InfoCommand",
    "Arikaim\\Core\\App\\Commands\\Extensions\\ListCommand",  
    "Arikaim\\Core\\App\\Commands\\Extensions\\UnInstallCommand",      
    "Arikaim\\Core\\App\\Commands\\Extensions\\EnableCommand",    
    "Arikaim\\Core\\App\\Commands\\Extensions\\DisableCommand",       
    "Arikaim\\Core\\App\\Commands\\Extensions\\InstallCommand",
    // modules
    "Arikaim\\Core\\App\\Commands\\Modules\\ListCommand",
    "Arikaim\\Core\\App\\Commands\\Modules\\InfoCommand",
    // drivers
    "Arikaim\\Core\\App\\Commands\\Drivers\\ListCommand",
    "Arikaim\\Core\\App\\Commands\\Drivers\\EnableCommand",
    "Arikaim\\Core\\App\\Commands\\Drivers\\DisableCommand",
    // UI library
    "Arikaim\\Core\\App\\Commands\\Library\\ListCommand",
    // templates
    "Arikaim\\Core\\App\\Commands\\Template\\ListCommand",
    "Arikaim\\Core\\App\\Commands\\Template\\InstallCommand"
];
