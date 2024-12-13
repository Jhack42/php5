<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\PreviewFinal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CarouselDesignControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** @test */
    public function it_can_create_a_carousel_design()
    {
        $designData = [
            'name' => 'Test Carousel',
            'width_type' => 'responsive',
            'width_value' => '100%',
            'height_type' => 'fixed',
            'height_value' => '400px',
            'background_type' => 'color',
            'background_color' => '#ffffff',
            'is_color_active' => true
        ];

        $response = $this->postJson('/api/carousel-design', $designData);

        $response->assertStatus(201)
                ->assertJson(['success' => true])
                ->assertJsonStructure(['design' => ['id_carousel_design']]);

        $this->assertDatabaseHas('carousel_designs', [
            'name' => 'Test Carousel',
            'width_type' => 'responsive'
        ]);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->postJson('/api/carousel-design', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_validates_width_type_enum()
    {
        $response = $this->postJson('/api/carousel-design', [
            'name' => 'Test Carousel',
            'width_type' => 'invalid_type'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['width_type']);
    }

    /** @test */
    public function it_can_upload_background_image()
    {
        $file = UploadedFile::fake()->image('carousel-bg.jpg');

        $response = $this->postJson('/api/carousel-design/upload-image', [
            'image' => $file
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure(['url']);

        Storage::disk('public')->assertExists('carousel-images/' . $file->hashName());
    }

    /** @test */
    public function it_can_upload_background_video()
    {
        $file = UploadedFile::fake()->create('video.mp4', 1024);

        $response = $this->postJson('/api/carousel-design/upload-video', [
            'video' => $file
        ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure(['url']);

        Storage::disk('public')->assertExists('carousel-videos/' . $file->hashName());
    }

    /** @test */
    public function it_can_update_existing_carousel_design()
    {
        $design = PreviewFinal::factory()->create();

        $updateData = [
            'name' => 'Updated Carousel',
            'background_color' => '#000000'
        ];

        $response = $this->putJson("/api/carousel-design/{$design->id_carousel_design}", $updateData);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $this->assertDatabaseHas('carousel_designs', [
            'id_carousel_design' => $design->id_carousel_design,
            'name' => 'Updated Carousel',
            'background_color' => '#000000'
        ]);
    }

    /** @test */
    public function it_can_get_active_design()
    {
        $activeDesign = PreviewFinal::factory()->create(['active' => 1]);
        PreviewFinal::factory()->create(['active' => 0]);

        $response = $this->getJson('/api/carousel-design/active');

        $response->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonPath('design.id_carousel_design', $activeDesign->id_carousel_design);
    }

    /** @test */
    public function it_generates_valid_css()
    {
        $design = PreviewFinal::factory()->create([
            'width_type' => 'fixed',
            'width_value' => '1200px',
            'height_value' => '400px',
            'background_type' => 'color',
            'background_color' => '#ffffff',
            'is_color_active' => true,
            'border_radius' => '10px',
            'custom_css' => '.custom-class { color: red; }'
        ]);

        $css = $design->generateCSS();

        $this->assertStringContainsString('width: 1200px', $css);
        $this->assertStringContainsString('height: 400px', $css);
        $this->assertStringContainsString('background-color: #ffffff', $css);
        $this->assertStringContainsString('border-radius: 10px', $css);
        $this->assertStringContainsString('.custom-class { color: red; }', $css);
    }

    /** @test */
    public function it_handles_breakpoints_correctly()
    {
        $breakpoints = [
            'mobile' => ['width' => '100%', 'height' => '300px'],
            'tablet' => ['width' => '90%', 'height' => '350px'],
            'desktop' => ['width' => '1200px', 'height' => '400px']
        ];

        $design = PreviewFinal::factory()->create([
            'is_responsive' => true,
            'breakpoints' => $breakpoints
        ]);

        $this->assertEquals($breakpoints, $design->breakpoints);
    }

    /** @test */
    public function it_can_deactivate_all_background_types()
    {
        $design = PreviewFinal::factory()->create([
            'background_type' => 'color',
            'is_color_active' => true,
            'is_image_active' => false,
            'is_video_active' => false
        ]);

        $response = $this->putJson("/api/carousel-design/{$design->id_carousel_design}", [
            'is_color_active' => false
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('carousel_designs', [
            'id_carousel_design' => $design->id_carousel_design,
            'is_color_active' => false,
            'is_image_active' => false,
            'is_video_active' => false
        ]);
    }

    /** @test */
    public function it_handles_file_validation()
    {
        // Prueba de imagen inválida
        $invalidImage = UploadedFile::fake()->create('invalid.txt');
        $response = $this->postJson('/api/carousel-design/upload-image', [
            'image' => $invalidImage
        ]);
        $response->assertStatus(422);

        // Prueba de video inválido
        $invalidVideo = UploadedFile::fake()->create('invalid.txt');
        $response = $this->postJson('/api/carousel-design/upload-video', [
            'video' => $invalidVideo
        ]);
        $response->assertStatus(422);
    }

    /** @test */
    public function it_prevents_duplicate_active_designs()
    {
        // Crear un diseño activo
        $activeDesign1 = PreviewFinal::factory()->create(['active' => 1]);

        // Intentar crear otro diseño activo
        $activeDesign2 = PreviewFinal::factory()->create(['active' => 1]);

        // Verificar que solo uno está activo
        $this->assertEquals(1, PreviewFinal::where('active', 1)->count());
    }
}
