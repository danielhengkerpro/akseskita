<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_report_without_photo(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->post(route('user.report.store'), [
                'place_name' => 'Stasiun Uji',
                'description' => 'Lift sedang rusak sejak kemarin pagi.',
            ])
            ->assertRedirect(route('user.dashboard'));

        $this->assertDatabaseHas('reports', [
            'user_id' => $user->id,
            'place_name' => 'Stasiun Uji',
            'status' => Report::STATUS_PENDING,
        ]);
    }

    public function test_user_can_submit_report_with_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        // PNG 1x1 asli (tanpa GD), agar valid sebagai image.
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQAY3Y2wAAAAAElFTkSuQmCC'
        );

        $this->actingAs($user)
            ->post(route('user.report.store'), [
                'place_name' => 'Mal Uji',
                'description' => 'Ramp tersedia namun licin saat hujan.',
                'photo' => UploadedFile::fake()->createWithContent('bukti.png', $png),
            ])
            ->assertRedirect(route('user.dashboard'));

        $report = Report::first();
        $this->assertNotNull($report->photo_path);
        Storage::disk('public')->assertExists($report->photo_path);
    }

    public function test_report_description_must_be_at_least_15_chars(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->post(route('user.report.store'), [
                'place_name' => 'Tempat',
                'description' => 'rusak',
            ])
            ->assertSessionHasErrors('description');
    }

    public function test_invalid_place_id_is_rejected(): void
    {
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->post(route('user.report.store'), [
                'place_id' => 999999,
                'place_name' => 'Tempat',
                'description' => 'Deskripsi yang cukup panjang untuk lolos validasi.',
            ])
            ->assertSessionHasErrors('place_id');
    }

    public function test_photo_must_be_a_valid_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->user()->create();

        $this->actingAs($user)
            ->post(route('user.report.store'), [
                'place_name' => 'Tempat',
                'description' => 'Deskripsi yang cukup panjang untuk lolos validasi.',
                'photo' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('photo');
    }

    public function test_report_create_form_escapes_user_input_in_js_context(): void
    {
        // Regresi XSS: nilai old() di dalam string JS harus di-escape
        // (via @js), sehingga payload tidak boleh keluar mentah-mentah.
        $user = User::factory()->user()->create();

        $payload = "x'-alert(1)-'";

        $response = $this->actingAs($user)
            ->from(route('user.report.create'))
            ->post(route('user.report.store'), [
                'place_name' => $payload,
                'description' => 'Deskripsi yang cukup panjang untuk lolos validasi.',
                'photo' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf'),
            ]);

        $response->assertSessionHasErrors('photo');

        // Ikuti redirect balik ke form; old() akan berisi payload.
        $html = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('user.report.store'), [
                'place_name' => $payload,
                'description' => 'Deskripsi yang cukup panjang untuk lolos validasi.',
                'photo' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf'),
            ])
            ->getContent();

        // Kutip tunggal mentah di dalam string JS tidak boleh muncul.
        $this->assertStringNotContainsString("placeName: '".$payload."'", $html);
    }
}
