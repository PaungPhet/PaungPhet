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

/**
 * @property Schema $form
 */
class ManageWedding extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-wedding';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;
    protected static ?string $slug = 'my-wedding';
    protected static ?string $title = 'မင်္ဂလာပွဲ';

    public ?array $data = [];

    public function mount(): void
    {
        $wedding = auth()->user()->wedding;

        if ($wedding) {
            $this->form->fill($wedding->attributesToArray());
        } else {
            $this->form->fill([
                'event_date' => now(),

                'content' => '<p style="text-align: center">ကာသီတိုင်း၊ ဗာရာဏသီမြို့နေ <strong>ဦး ~</strong> + <strong>ဒေါ် ~</strong> တို့၏ သား</p>
<h2 style="text-align: center">မောင် ~</h2>
<p style="text-align: center">နှင့်</p>
<p style="text-align: center">ကာသီတိုင်း၊ ဗာရာဏသီမြို့နေ <strong>ဦး ~</strong> + <strong>ဒေါ် ~</strong> တို့၏ သမီး</p>
<h2 style="text-align: center">မ ~</h2>
<p style="text-align: center">တို့၏ <strong>မင်္ဂလာ ဧည့်ခံပွဲ</strong> သို့ ကြွရောက်ပါရန် ကျွန်တော် + ကျွန်မတို့နှင့် တကွ နှစ်ဖက်သော မိဘများမှ ခင်မင်လေးစားစွာ ဖိတ်ကြားအပ်ပါသည်။</p>
'
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
                Section::make('ဖူးစာဖက်')
                    ->columns()
                    ->schema([
                        TextInput::make('partner_one')
                            ->required()
                            ->label('သတို့သား')
                            ->placeholder('မောင်'),
                        TextInput::make('partner_two')
                            ->required()
                            ->label('သတို့သမီး')
                            ->placeholder('မေ'),
                        TextInput::make('slug')
                            ->required()
                            ->unique(table: 'weddings', column: 'slug', ignorable: fn() => auth()->user()->wedding)
                            ->prefix(config('app.url') . '/')
                            ->placeholder('mg-and-may')
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label('မင်္ဂလာဧည့်ခံပွဲ ဖိတ်ကြားလွှာ')
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
                    Section::make('မင်္ဂလာ အစီအစဥ်')
                        ->schema([
                            DatePicker::make('event_date')
                                ->label('မင်္ဂလာ နေ့ရက်')
                                ->live()
                                ->hint(fn(Get $get) => Carbon::parse($get('event_date'))->locale('my')->translatedFormat('Y ခု၊ F j ရက် (lနေ့)'))
                                ->required(),
                            TextInput::make('event_time')
                                ->label('မင်္ဂလာ အချိန်')
                                ->placeholder('နံနက် ၆ နာရီမှ ၁၂ နာရီ အထိ'),
                            Textarea::make('မင်္ဂလာ နေရာ')
                                ->rows(2)
                                ->placeholder('- ၏ နေအိမ်မင်္ဂလာမဏ္ဍပ်သို့')
                            ,
                            TextInput::make('address_url')
                                ->label('မင်္ဂလာ နေရာ Maps URL')
                                ->placeholder('https://maps.app.goo.gl/...')

                        ]),
                ])->columns(1),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('သိမ်းဆည်းမည်')
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
            ->title('Wedding details saved successfully')
            ->send();
    }

}
