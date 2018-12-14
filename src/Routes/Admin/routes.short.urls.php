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

Route::domain(config('hc.admin_domain'))
    ->prefix(config('hc.admin_url'))
    ->namespace('Admin')
    ->middleware(['web', 'auth'])
    ->group(function () {
        Route::get('short-urls', 'HCShortUrlController@index')
            ->name('admin.short.urls.index')
            ->middleware('acl:honey_comb_short_url_short_urls_admin_list');

        Route::prefix('api/short-urls')->group(function () {
            Route::get('/', 'HCShortUrlController@getListPaginate')
                ->name('admin.api.short.urls')
                ->middleware('acl:honey_comb_short_url_short_urls_admin_list');

            Route::get('list', 'HCShortUrlController@getList')
                ->name('admin.api.short.urls.list')
                ->middleware('acl:honey_comb_short_url_short_urls_admin_list');

            Route::get('options', 'HCShortUrlController@getOptions')
                ->name('admin.api.short.urls.list');

            Route::post('/', 'HCShortUrlController@store')
                ->name('admin.api.short.urls.create')
                ->middleware('acl:honey_comb_short_url_short_urls_admin_create');

            Route::delete('/', 'HCShortUrlController@deleteSoft')
                ->name('admin.api.short.urls.delete')
                ->middleware('acl:honey_comb_short_url_short_urls_admin_delete');

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'HCShortUrlController@getById')
                    ->name('admin.api.short.urls.single')
                    ->middleware('acl:honey_comb_short_url_short_urls_admin_list');

                Route::put('/', 'HCShortUrlController@update')
                    ->name('admin.api.short.urls.update')
                    ->middleware('acl:honey_comb_short_url_short_urls_admin_update');

                Route::patch('/', 'HCShortUrlController@patch')
                    ->name('admin.api.short.urls.patch')
                    ->middleware('acl:honey_comb_short_url_short_urls_admin_update');
            });
        });
    });
