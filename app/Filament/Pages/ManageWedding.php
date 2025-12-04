<?php

namespace App\Filament\Pages;

use App\Models\Wedding;
use BackedEnum;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Schema $form
 */
class ManageWedding extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-wedding';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;
    protected static ?string $slug = 'my-wedding';

    public function getHeading(): string|Htmlable|null
    {
        return __('filament/admin/manage_wedding.title');
    }

    /**
     * @return string
     */
    public static function getNavigationLabel(): string
    {
        return __('filament/admin/manage_wedding.title');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $wedding = auth()->user()->wedding;

        if ($wedding) {
            $this->form->fill($wedding->attributesToArray());
        } else {
            $this->form->fill([
                'event_date' => now(),
                'event_time' => __('filament/admin/manage_wedding.event_time_default'),
                'address' => __('filament/admin/manage_wedding.address_default'),
                'content' => __('filament/admin/manage_wedding.content_default'),
            ]);
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->model(Wedding::class)
            ->columns()
            ->schema([
                Section::make(__('filament/admin/manage_wedding.partners'))
                    ->columns()
                    ->schema([
                        TextInput::make('partner_one')
                            ->required()
                            ->label(__('filament/admin/manage_wedding.partner_one'))
                            ->placeholder(__('filament/admin/manage_wedding.partner_one_placeholder')),
                        TextInput::make('partner_two')
                            ->required()
                            ->label(__('filament/admin/manage_wedding.partner_two'))
                            ->placeholder(__('filament/admin/manage_wedding.partner_two_placeholder')),
                        TextInput::make('slug')
                            ->required()
                            ->unique(table: 'weddings', column: 'slug', ignorable: fn() => auth()->user()->wedding)
                            ->prefix(config('app.url') . '/')
                            ->placeholder('mg-and-may')
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label(__('filament/admin/manage_wedding.content'))
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('contents/' . auth()->id())
                            ->fileAttachmentsVisibility('public')
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                ['h1', 'h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'],
                                ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                ['textColor', 'table', 'grid', 'attachFiles'],
                                ['undo', 'redo'],
                            ])
                            ->columnSpanFull()
                    ]),

                Grid::make()->schema([
                    Section::make(__('filament/admin/manage_wedding.wedding_details'))
                        ->schema([
                            DatePicker::make('event_date')
                                ->label(__('filament/admin/manage_wedding.event_date'))
                                ->live()
                                ->hint(fn(Get $get) => Carbon::parse($get('event_date'))->translatedFormat(__('filament/admin/manage_wedding.event_date_format')))
                                ->required(),
                            TextInput::make('event_time')
                                ->label(__('filament/admin/manage_wedding.event_time')),
                            Textarea::make('address')
                                ->label(__('filament/admin/manage_wedding.address'))
                                ->rows(2),
                            TextInput::make('address_url')
                                ->label(__('filament/admin/manage_wedding.address_url'))
                                ->placeholder('https://maps.app.goo.gl/...')

                        ]),
                ])->columns(1),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/admin/manage_wedding.save'))
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        $user->wedding()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        Notification::make()
            ->success()
            ->title(__('filament/admin/manage_wedding.wedding_details_saved_successfully'))
            ->send();
    }

    public function getTitle(): string
    {
        return __('filament/admin/manage_wedding.title');
    }


}
