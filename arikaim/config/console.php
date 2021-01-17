<?php
/**
 * Console commands classes
 */
return [
    'Arikaim\\Core\\App\\Commands\\HelpCommand',      
    'Arikaim\\Core\\App\\Commands\\ClearLogsCommand',
    'Arikaim\\Core\\App\\Commands\\AdminResetCommand',
    'Arikaim\\Core\\App\\Commands\\ComposerCommand',
    // install
    'Arikaim\\Core\\App\\Commands\\Install\\InstallCommand',
    'Arikaim\\Core\\App\\Commands\\Install\\EnableInstallCommand',
    'Arikaim\\Core\\App\\Commands\\Install\\DisableInstallCommand',
    'Arikaim\\Core\\App\\Commands\\Install\\RepairInstallCommand',
    'Arikaim\\Core\\App\\Commands\\Install\\PrepareCommand',
    // cache
    'Arikaim\\Core\\App\\Commands\\Cache\\ClearCommand',
    'Arikaim\\Core\\App\\Commands\\Cache\\DriverCommand',
    'Arikaim\\Core\\App\\Commands\\Cache\\EnableCommand',
    'Arikaim\\Core\\App\\Commands\\Cache\\DisableCommand',
    // session
    'Arikaim\\Core\\App\\Commands\\Session\\RestartCommand',
    'Arikaim\\Core\\App\\Commands\\Session\\InfoCommand',
    // queue
    'Arikaim\\Core\\App\\Commands\\Queue\\JobsCommand',      
    'Arikaim\\Core\\App\\Commands\\Queue\\CronCommand',
    'Arikaim\\Core\\App\\Commands\\Queue\\RunJobCommand',
    'Arikaim\\Core\\App\\Commands\\Queue\\JobDetailsCommand',    
    // extensions
    'Arikaim\\Core\\App\\Commands\\Extensions\\InfoCommand',
    'Arikaim\\Core\\App\\Commands\\Extensions\\ListCommand',  
    'Arikaim\\Core\\App\\Commands\\Extensions\\UnInstallCommand',      
    'Arikaim\\Core\\App\\Commands\\Extensions\\EnableCommand',    
    'Arikaim\\Core\\App\\Commands\\Extensions\\DisableCommand',       
    'Arikaim\\Core\\App\\Commands\\Extensions\\InstallCommand',
    // modules
    'Arikaim\\Core\\App\\Commands\\Modules\\ListCommand',
    'Arikaim\\Core\\App\\Commands\\Modules\\InfoCommand',
    // drivers
    'Arikaim\\Core\\App\\Commands\\Drivers\\ListCommand',
    'Arikaim\\Core\\App\\Commands\\Drivers\\EnableCommand',
    'Arikaim\\Core\\App\\Commands\\Drivers\\DisableCommand',
    // UI library
    'Arikaim\\Core\\App\\Commands\\Library\\ListCommand',
    // templates
    'Arikaim\\Core\\App\\Commands\\Template\\ListCommand',
    'Arikaim\\Core\\App\\Commands\\Template\\InstallCommand',
    // events
    'Arikaim\\Core\\App\\Commands\\Events\\EventDetailsCommand'
];
