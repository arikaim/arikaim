<?php
/**
 * Console commands classes
 */
return [
    "Arikaim\\Core\\System\\Console\\Commands\\HelpCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\UpdateCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\InstallCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\ShellCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\ClearLogsCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\AdminResetCommand",
    // cache
    "Arikaim\\Core\\System\\Console\\Commands\\Cache\\ClearCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Cache\\EnableCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Cache\\DisableCommand",
    // session
    "Arikaim\\Core\\System\\Console\\Commands\\Session\\RestartCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Session\\InfoCommand",
    // queue
    'Arikaim\\Core\\System\\Console\\Commands\\Queue\\JobsCommand',
    'Arikaim\\Core\\System\\Console\\Commands\\Queue\\WorkerCommand',
    'Arikaim\\Core\\System\\Console\\Commands\\Queue\\StopCommand',
    'Arikaim\\Core\\System\\Console\\Commands\\Queue\\CronCommand',
    // job
    'Arikaim\\Core\\System\\Console\\Commands\\Job\\RunJobCommand',
    // extensions
    "Arikaim\\Core\\System\\Console\\Commands\\Extensions\\InfoCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Extensions\\ListCommand",  
    "Arikaim\\Core\\System\\Console\\Commands\\Extensions\\UnInstallCommand",      
    "Arikaim\\Core\\System\\Console\\Commands\\Extensions\\EnableCommand",    
    "Arikaim\\Core\\System\\Console\\Commands\\Extensions\\DisableCommand",       
    "Arikaim\\Core\\System\\Console\\Commands\\Extensions\\InstallCommand",
    // modules
    "Arikaim\\Core\\System\\Console\\Commands\\Modules\\ListCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Modules\\InfoCommand",
    // drivers
    "Arikaim\\Core\\System\\Console\\Commands\\Drivers\\ListCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Drivers\\EnableCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Drivers\\DisableCommand",
    // UI library
    "Arikaim\\Core\\System\\Console\\Commands\\Library\\ListCommand",
    // templates
    "Arikaim\\Core\\System\\Console\\Commands\\Template\\ListCommand",
    "Arikaim\\Core\\System\\Console\\Commands\\Template\\InstallCommand"
];
