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

namespace HoneyComb\ShortUrl\Services\Admin;

use HoneyComb\ShortUrl\Models\HCShortUrl;
use HoneyComb\ShortUrl\Repositories\Admin\HCShortUrlRepository;

/**
 * Class HCShortUrlService
 * @package HoneyComb\ShortUrl\Services\Admin
 */
class HCShortUrlService
{
    /**
     * @var HCShortUrlRepository
     */
    private $repository;

    /**
     * HCShortUrlService constructor.
     * @param HCShortUrlRepository $repository
     */
    public function __construct(HCShortUrlRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return HCShortUrlRepository
     */
    public function getRepository(): HCShortUrlRepository
    {
        return $this->repository;
    }

    /**
     * @param string $url
     * @param int $length
     * @return HCShortUrl
     */
    public function generate(string $url, int $length = 6): HCShortUrl
    {
        return $this->getRepository()->create([
            'url' => $url,
            'key' => $this->getUniqueKey($length),
        ]);
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getUrlByKeyAndIncrementClicks(string $key): ?string
    {
        $record = $this->getRepository()->findOneBy(['key' => $key]);

        if (is_null($record)) {
            return null;
        }

        $record->increment('clicks');

        return $record->url;
    }

    /**
     * @param int $length
     * @return string
     */
    private function getUniqueKey(int $length): string
    {
        $unique = false;

        while (!$unique) {
            $key = str_random($length);

            $unique = $this->getRepository()->isKeyUnique($key);
        }

        return $key;
    }
}
