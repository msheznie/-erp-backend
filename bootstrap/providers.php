<?php

return [
    /*
     * Laravel Framework Service Providers...
     */
    Illuminate\Auth\AuthServiceProvider::class,
    Illuminate\Broadcasting\BroadcastServiceProvider::class,
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
    Illuminate\Cookie\CookieServiceProvider::class,
    Illuminate\Database\DatabaseServiceProvider::class,
    Illuminate\Encryption\EncryptionServiceProvider::class,
    Illuminate\Filesystem\FilesystemServiceProvider::class,
    Illuminate\Foundation\Providers\FoundationServiceProvider::class,
    Illuminate\Hashing\HashServiceProvider::class,
    Illuminate\Notifications\NotificationServiceProvider::class,
    Illuminate\Pagination\PaginationServiceProvider::class,
    Illuminate\Pipeline\PipelineServiceProvider::class,
    Illuminate\Queue\QueueServiceProvider::class,
    Illuminate\Redis\RedisServiceProvider::class,
    Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
    Illuminate\Session\SessionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Illuminate\Validation\ValidationServiceProvider::class,
    Illuminate\View\ViewServiceProvider::class,

    /*
     * Package Service Providers...
     */
    Yajra\DataTables\DataTablesServiceProvider::class,
    Maatwebsite\Excel\ExcelServiceProvider::class,
    Barryvdh\DomPDF\ServiceProvider::class,
    Seguce92\DomPDF\ServiceProvider::class,

    /*
     * Application Service Providers...
     */
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,
    App\Providers\RouteServiceProvider::class,
    Collective\Html\HtmlServiceProvider::class,
    Laracasts\Flash\FlashServiceProvider::class,
    Prettus\Repository\Providers\RepositoryServiceProvider::class,
    \InfyOm\Generator\InfyOmGeneratorServiceProvider::class,
    \InfyOm\AdminLTETemplates\AdminLTETemplatesServiceProvider::class,
    App\Providers\HelperServiceProvider::class,
    Sichikawa\LaravelSendgridDriver\MailServiceProvider::class,
    Rap2hpoutre\LaravelLogViewer\LaravelLogViewerServiceProvider::class,
    LaravelFCM\FCMServiceProvider::class,
    Barryvdh\Debugbar\ServiceProvider::class,
];
