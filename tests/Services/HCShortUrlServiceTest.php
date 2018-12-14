<?php
/**
 * @copyright 2018 innovationbase
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * Contact InnovationBase:
 * E-mail: hello@innovationbase.eu
 * https://innovationbase.eu
 */

declare(strict_types = 1);

namespace Tests\Services;

use HoneyComb\ShortUrl\Models\HCShortUrl;
use HoneyComb\ShortUrl\Services\Admin\HCShortUrlService;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class HCShortUrlServiceTest
 * @package Tests\Services
 */
class HCShortUrlServiceTest extends TestCase
{
    use RefreshDatabase, InteractsWithDatabase;

    /**
     * @test
     * @group short-url-service
     */
    public function it_must_create_singleton_instance(): void
    {
        $this->assertInstanceOf(HCShortUrlService::class, $this->getTestClassInstance());

        $this->assertSame($this->getTestClassInstance(), $this->getTestClassInstance());
    }

    /**
     * @test
     * @group short-url-service
     */
    public function it_must_generate_code(): void
    {
        $url = 'https://github.com/honey-comb/';

        $result = $this->getTestClassInstance()->generate($url);

        $this->assertInstanceOf(HCShortUrl::class, $result);

        $this->assertDatabaseHas('hc_short_url', [
            'url' => $url,
            'clicks' => 0,
        ]);
    }

    /**
     * @test
     * @group short-url-service
     */
    public function it_must_find_url_by_code_and_increment_clicks_and_also_return_string(): void
    {
        $url = 'https://github.com/honey-comb/';
        $fake = $this->getTestClassInstance()->generate($url);

        $result = $this->getTestClassInstance()->getUrlByKeyAndIncrementClicks($fake->key);

        $this->assertSame($result, $url);
        $this->assertDatabaseHas('hc_short_url', [
            'url' => $url,
            'clicks' => 1,
        ]);

        $this->getTestClassInstance()->getUrlByKeyAndIncrementClicks($fake->key);
        $this->getTestClassInstance()->getUrlByKeyAndIncrementClicks($fake->key);
        $this->getTestClassInstance()->getUrlByKeyAndIncrementClicks($fake->key);

        $this->assertDatabaseHas('hc_short_url', [
            'url' => $url,
            'clicks' => 4,
        ]);

    }


    /**
     * @return HCShortUrlService
     */
    private function getTestClassInstance(): HCShortUrlService
    {
        return $this->app->make(HCShortUrlService::class);
    }
}
