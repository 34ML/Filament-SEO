<?php

use _34ML\SEO\SEO;
use _34ml\SEO\Tests\Fixtures\Http\Livewire\CreatePost;
use _34ml\SEO\Tests\Fixtures\Http\Livewire\EditPost;
use _34ml\SEO\Tests\Fixtures\Models\Post;
use Livewire\Livewire;

test('can create post with adding en seo data', function () {
    $livewire = Livewire::test(CreatePost::class);

    $livewire
        ->set('data.title', 'Post title')
        ->set('data.seo.en_title', 'seo title')
        ->set('data.seo.en_description', 'seo description')
        ->set('data.seo.en_keywords', 'seo keywords')
        ->set('data.seo.follow', 'follow')
        ->call('submitForm')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(Post::class, [
        'title' => 'Post title',
    ]);

    $this->assertDatabaseHas(SEO::class, [
        'title' => "{\"en\":\"seo title\"}",
        'description' => "{\"en\":\"seo description\"}",
        'keywords' => "{\"en\":\"seo keywords\"}",
        'follow_type' => 'follow',
    ]);
});

test('can update a post without a seo model', function () {
    $post = Post::create([
        'title' => 'Test post',
    ]);

    $livewire = Livewire::test(EditPost::class, [
        'post' => $post,
    ]);

    $this->assertDatabaseCount(SEO::class, 0);

    $livewire
        ->set('data.title', 'Post title')
        ->set('data.seo.en_title', 'seo title')
        ->set('data.seo.en_description', 'seo description')
        ->set('data.seo.en_keywords', 'seo keywords')
        ->set('data.seo.follow', 'follow')
        ->call('submitForm')
        ->assertHasNoErrors();

    $this->assertDatabaseHas(Post::class, [
        'title' => 'Post title',
    ]);

    $this->assertDatabaseHas(SEO::class, [
        'title' => "{\"en\":\"seo title\"}",
        'description' => "{\"en\":\"seo description\"}",
        'keywords' => "{\"en\":\"seo keywords\"}",
        'follow' => 'follow',
    ]);
});

test('can update the post with seo', function () {
    $post = Post::create([
        'title' => 'Post title',
    ]);

    EditPost::$seoFields = [];

    $livewire = Livewire::test(EditPost::class, [
        'post' => $post,
    ]);

    $livewire
        ->assertSet('post', $post)
        ->set('data.title', 'Post title #2')
        ->set('data.seo.en_title', 'seo title #2')
        ->set('data.seo.en_description', '')
        ->set('data.seo.en_keywords', '')
        ->set('data.seo.follow', 'nofollow')
        ->call('submitForm')
        ->assertHasNoErrors();

    expect($post->refresh())->title->toBe('Post title #2');

    $this->assertDatabaseHas(Post::class, [
        'title' => 'Post title #2',
    ]);

    $this->assertDatabaseHas(SEO::class, [
        'title' => "{\"en\":\"seo title #2\"}",
        'description' => "{\"en\":null}",
        "keywords" => "{\"en\":null}",
        'follow' => 'follow',
    ]);
});
