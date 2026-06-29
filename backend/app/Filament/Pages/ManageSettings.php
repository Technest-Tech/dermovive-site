<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Site Settings';

    protected static ?string $navigationLabel = 'Site Settings';

    protected static string $view = 'filament.pages.manage-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** Setting keys managed by this page. */
    protected const KEYS = [
        'site_name', 'tagline', 'contact_email', 'contact_phone',
        'address', 'instagram_url', 'facebook_url', 'whatsapp_number',
    ];

    public function mount(): void
    {
        $this->form->fill(
            collect(self::KEYS)->mapWithKeys(fn ($k) => [$k => Setting::get($k)])->all()
        );
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Brand')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('site_name')->maxLength(255),
                        Forms\Components\TextInput::make('tagline')->maxLength(255),
                    ]),
                Forms\Components\Section::make('Contact')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('contact_email')->email(),
                        Forms\Components\TextInput::make('contact_phone')->tel(),
                        Forms\Components\Textarea::make('address')->rows(2)->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Social')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('instagram_url')->url()->label('Instagram URL'),
                        Forms\Components\TextInput::make('facebook_url')->url()->label('Facebook URL'),
                        Forms\Components\TextInput::make('whatsapp_number')->tel()->label('WhatsApp number'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        foreach ($this->form->getState() as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()->title('Settings saved')->success()->send();
    }
}
