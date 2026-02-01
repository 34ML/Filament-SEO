<?php

namespace _34ml\SEO\Tests\Fixtures\Http\Livewire;

use _34ml\SEO\SEOField;
use _34ml\SEO\Tests\Fixtures\Models\Post;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\MessageBag;
use Livewire\Component;

class CreatePost extends Component implements HasForms
{
    use InteractsWithForms;

    public array $data = [];

    public static $seoFields = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function getErrorBag()
    {
        $errorBag = parent::getErrorBag();
        return $errorBag ?? new MessageBag();
    }

    public function render(): View
    {
        return view('livewire.create-post');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title'),
                ...SEOField::make(),
            ])
            ->statePath('data')
            ->model(Post::class);
    }

    public function submitForm(): void
    {
        $state = $this->form->getState();

        $post = Post::create(
            Arr::except($state, ['seo'])
        );

        $this->form->model($post)->saveRelationships();
        $post->refresh();
    }
}
