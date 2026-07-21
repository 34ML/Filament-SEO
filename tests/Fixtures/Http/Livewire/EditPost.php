<?php

namespace _34ml\SEO\Tests\Fixtures\Http\Livewire;

use _34ml\SEO\SEOField;
use _34ml\SEO\Tests\Fixtures\Models\Post;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Livewire\Component;

class EditPost extends Component implements HasForms
{
    use InteractsWithForms;

    public array $data = [];

    public Post $post;

    public static $seoFields = [];

    public function mount(): void
    {
        $this->form->fill([
            'title' => $this->post['title'],
        ]);
    }

    public function getErrorBag()
    {
        $errorBag = parent::getErrorBag();

        return $errorBag ?? new MessageBag;
    }

    public function render(): View
    {
        return view('livewire.edit-post');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')->required(),
                ...SEOField::make(),
            ])
            ->statePath('data')
            ->model($this->post);
    }

    public function submitForm(): void
    {
        $data = $this->form->getState();

        $this->post->update(
            Arr::except($data, ['seo'])
        );

        $this->form->model($this->post)->saveRelationships();
        $this->post->refresh();
    }
}
