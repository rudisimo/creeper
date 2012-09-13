<?php

/*
 * This file is part of the Creeper package.
 *
 * (c) Rodolfo Puig <rpuig@7gstudios.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/../vendor/autoload.php';

use Creeper\Bundle\Entity\OpenGraph;
use Goutte\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Create default application
$app = new Silex\Application();

// Enable debugging
if (in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $app['debug'] = TRUE;
}

// Create the URL parsing route
$app->match('/parse/url', function(Request $request) {
    $response = new Response();
    $input = $request->get('input', NULL);
    if ($input !== NULL) {
        // Create the URL object
        $url = parse_url($input);
        if (isset($url['query'])) {
            parse_str($url['query'], $query);
            ksort($query);
            $url['query'] = $query;
        }

        // Create the Goutte client/crawler object
        $client = new Client();
        //$client->setHeader('User-Agent', 'facebookexternalhit/1.1 (+https://www.facebook.com/externalhit_uatext.php)'); // Amazon hides the Open Graph data from everyone except Facebook
        $crawler = $client->request('GET', $input);

        // Create the OGP object
        $ogp = new OpenGraph();
        // Set the OGP URL
        $ogpUrl = $crawler->filterXPath('//head//meta[@property="og:url"]');
        if (count($ogpUrl) > 0) {
            $ogp->setUrl($ogpUrl->attr('content'));
        }
        $ogpUrl = $ogp->getUrl();
        if ($ogpUrl == NULL) {
            $ogp->setUrl($url);
        }
        // Set the OGP title
        $ogpTitle = $crawler->filterXPath('//head//meta[@property="og:title"]');
        if (count($ogpTitle) > 0) {
            $ogp->setTitle($ogpTitle->attr('content'));
        }
        $ogpTitle = $ogp->getTitle();
        if ($ogpTitle == NULL) {
            $ogpTitle = $crawler->filter('title');
            if (count($ogpTitle) > 0) {
                $ogp->setTitle($ogpTitle->text());
            }
        }
        // Set the OGP image
        $ogpImage = $crawler->filterXPath('//head//meta[@property="og:image"]');
        if (count($ogpImage) > 0) {
            $ogp->setImage($ogpImage->attr('content'));
        }

        $response->headers->set('Content-type', 'application/json');
        $response->setContent($ogp->json());
    } else {
        $response->setStatusCode(400);
    }

    return $response;
})->method('GET|POST');

// Run the default application
$app->run();