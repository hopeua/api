<?php
namespace Hope\RestBundle\Tests\Controller;

use Hope\RestBundle\Tests\RestTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends RestTestCase
{
    /**
     * @large
     */
    public function testRequest()
    {
        $client = static::createClient();

        $client->request('GET', '/v1/home.json');

        $this->assertEquals(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode()
        );
        $response = $client->getResponse()->getContent();

        $data = json_decode($response);
        $this->assertInstanceOf('stdClass', $data);

        return $data;
    }

    /**
     * @large
     * @depends testRequest
     */
    public function testBanners($data)
    {
        $this->assertObjectHasAttribute('banners', $data);
        $banners = $data->banners;

        $this->assertInternalType('array', $banners);

        if (count($banners)) {
            $banner = $banners[array_rand($banners)];

            $this->assertInstanceOf('stdClass', $banner);
            $this->assertObjectHasAttribute('image', $banner);
            $this->assertNotEmpty($banner->image);
            $this->assertObjectHasAttribute('url', $banner);
            $this->assertNotEmpty($banner->url);
        }
    }

    /**
     * @large
     * @depends testRequest
     */
    public function testLive($data)
    {
        $this->assertObjectHasAttribute('live', $data);
        $live = $data->live;

        $this->assertInstanceOf('stdClass', $live);
        $this->assertObjectHasAttribute('stream', $live);
        $this->assertNotEmpty($live->stream);
    }

    /**
     * @large
     * @depends testRequest
     */
    public function testCategories($data)
    {
        $this->assertObjectHasAttribute('categories', $data);
        $categories = $data->categories;

        $this->assertInternalType('array', $categories);
        $this->assertGreaterThan(0, count($categories));

        $cat = $categories[array_rand($categories)];

        $this->assertObjectHasAttribute('id', $cat);
        $this->assertInternalType('integer', $cat->id);

        $this->assertObjectHasAttribute('title', $cat);
        $this->assertNotEmpty($cat->title);

        $this->assertObjectHasAttribute('programs', $cat);
        return $cat->programs;
    }

    /**
     * @large
     * @depends testCategories
     */
    public function testPrograms($programs)
    {
        $this->assertInternalType('array', $programs);
        if (count($programs)) {
            $program = $programs[array_rand($programs)];
            $this->assertInstanceOf('stdClass', $program);

            $attrs = [
                'code' => [
                    'required' => true,
                    'regex'    => '^[A-Z]{4}$',
                ],
                'title' => [
                    'required' => true,
                ],
                'desc_short' => [],
                'desc_full'  => [],
            ];
            $this->checkAttributes($program, $attrs);
        }
    }

    /**
     * @large
     * @depends testRequest
     */
    public function testVideos($data)
    {
        $this->objectHasAttribute('top_videos', $data);
        $videos = $data->top_videos;

        $this->assertInternalType('array', $videos);
        $this->assertGreaterThan(0, count($videos));

        $video = $videos[array_rand($videos)];
        $this->assertInstanceOf('stdClass', $video);

        $attrs = [
            'code'         => [
                'required' => true,
                'regex'    => '^[A-Z]{4}\d{5}$',
            ],
            'title'        => [
                'required' => true,
            ],
            'desc'         => [],
            'author'       => [],
            'program'      => [
                'required' => true,
                'regex'    => '^[A-Z]{4}$',
            ],
            'duration'     => [
                'type' => 'integer',
            ],
            'publish_time' => [
                'regex' => '\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}',
            ],
            'hd'           => [
                'type' => 'boolean'
            ],
            'image'        => [
                'required' => true
            ],
        ];
        $this->checkAttributes($video, $attrs);

        $this->assertObjectHasAttribute('link', $video);

        $link = $video->link;
        $this->assertInstanceOf('stdClass', $link);
        $this->assertObjectHasAttribute('download', $link);
        $this->assertAttributeNotEmpty('download', $link);
        $this->assertObjectHasAttribute('watch', $link);
        $this->assertAttributeNotEmpty('watch', $link);
    }

    /**
     * @large
     * @depends testRequest
     */
    public function testAbout($data)
    {
        $this->objectHasAttribute('about', $data);
        $about = $data->about;

        $this->assertInternalType('array', $about);
        $this->assertEquals(3, count($about));

        foreach ($about as $page) {
            $this->assertInstanceOf('stdClass', $page);

            $attrs = [
                'section' => [
                    'required' => true,
                    'regex'    => '^a-z+$',
                ],
                'title'   => [
                    'required' => true,
                ],
                'text'    => [
                    'required' => true,
                ],
            ];
            $this->checkAttributes($page, $attrs);
        }
    }
}