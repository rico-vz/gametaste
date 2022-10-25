<?php

use RedBeanPHP\R as R;

require '../public/api/igdb/class.igdb.php';

class GameController extends BaseController
{
    public function view()
    {
        if (!isset($_GET['id'])) {
            error(404, 'No game ID specified');
            die();
        }

        $igdb = new IGDB("UR_API_KEY", "UR_API_KEY");
        $builder = new IGDBQueryBuilder();
        $result = $igdb->game(
            $builder
                ->name("Game with ID of " . $_GET['id'])
                ->fields("id, name, release_dates.human, rating_count, release_dates.date, release_dates.y, genres.name, rating, summary, cover.*")
                ->where("id = " . $_GET['id'])
                ->build()
        );

        $reviews = R::getAll('SELECT * FROM reviews WHERE game_id = ?', [$result[0]->id]);
        if (sizeof($reviews) == 0) {
            $reviews = null;
        }

        if (isset($_SESSION['loggedInUser'])) {
            $user = R::getCell('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        }

        // Select rating from reviews. Each character in rating is +1

        $avgRating = null;
        $ratingCount = null;
        $ratingStars = '';
        if ($reviews != null) {
            $rating = 0;
            foreach ($reviews as $review) {
                $num = 0;
                switch ($review['rating']) {
                    case '★':
                        $num = 1;
                        break;
                    case '★★':
                        $num = 2;
                        break;
                    case '★★★':
                        $num = 3;
                        break;
                    case '★★★★':
                        $num = 4;
                        break;
                    case '★★★★★':
                        $num = 5;
                        break;
                    default:
                        $num = 0;
                        break;
                }
                $rating += $num;

                $avgRating = $rating / sizeof($reviews);
                $ratingCount = sizeof($reviews);
            }
        }


        $data = [
            'gameName' =>  $result[0]->name,
            'gameRating' =>  $result[0]->rating ?? null,
            'gameRatingCount' =>  $result[0]->rating_count ?? null,
            'gameID' =>  $result[0]->id,
            'gameCover' =>  IGDBUtils::image_url($result[0]->cover->image_id, '1080p'),
            'gameDate' =>  $result[0]->release_dates[0]->human,
            'gameSummary' =>  $result[0]->summary,
            'gameGenre' =>  $result[0]->genres[0]->name,
            'reviews' => $reviews ?? null,
            'user' => $user ?? null,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            'gtRating' => $avgRating,
            'gtRatingCount' => $ratingCount ?? null,
        ];

        displayTemplate('game/view.html', $data);
    }
}