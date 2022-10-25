<?php

use RedBeanPHP\R as R;

class ReviewController extends BaseController
{
    public function show()
    {
        if (empty($_GET['id'])) {
            error(404, 'Geen ID opgegeven');
        }

        $review = $this->getBeanById('reviews', $_GET['id']);
        if (is_null($review)) {
            error(404, 'Review niet gevonden met ID ' . $_GET['id']);
        }

        $username = R::getCell('SELECT username FROM reviews WHERE id = ?', [$_GET['id']]);
        $displayname = R::getCell('SELECT display_name FROM users WHERE username = ?', [$username]);

        $pageViewer = R::getCell('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);

        $isAuthor = false;
        if ($pageViewer == $username) {
            $isAuthor = true;
        }

        $gameID = R::getCell('SELECT game_id FROM reviews WHERE id = ?', [$_GET['id']]);
        // instantiating the wrapper
        $igdb = new IGDB("k0c3065725802ija8esaw24v3tlh8h", "fta0z7aobuzq6n6eeobii4ilrn6iru");
        $builder = new IGDBQueryBuilder();
        $result = $igdb->game(
            $builder
                ->name("Game with ID of " . $gameID)
                ->fields("id, name, release_dates.human, rating_count, release_dates.date, release_dates.y, genres.name, rating, summary, cover.*")
                ->where("id = " . $gameID)
                ->build()
        );

        $data = [
            'gameName' =>  $result[0]->name,
            'gameRating' =>  $result[0]->rating ?? null,
            'gameRatingCount' =>  $result[0]->rating_count ?? null,
            'gameID' =>  $gameID,
            'gameCover' =>  IGDBUtils::image_url($result[0]->cover->image_id, '1080p'),
            'gameDate' =>  $result[0]->release_dates[0]->human,
            'gameGenre' =>  $result[0]->genres[0]->name,
            'reviews' => $reviews ?? null,
            'displayname' => $displayname,
            'id' => $_GET['id'],
            'review' => $review,
            'result' => $result,
            'isAuthor' => $isAuthor ?? null,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,

        ];

        displayTemplate('review/show.html', $data);
    }

    public function createReview()
    {
        $this->authorizeUser();

        $gameID = $_POST['game_id'];

        $igdb = new IGDB("k0c3065725802ija8esaw24v3tlh8h", "fta0z7aobuzq6n6eeobii4ilrn6iru");
        $builder = new IGDBQueryBuilder();

        $result = $igdb->game(
            $builder
                ->name("Game with ID of " . $gameID)
                ->fields("id, name, release_dates.human, rating_count, release_dates.date, release_dates.y, genres.name, rating, summary, cover.*")
                ->where("id = " . $gameID)
                ->build()
        );

        $data = [
            'gameName' =>  $result[0]->name,
            'gameRating' =>  $result[0]->rating ?? null,
            'gameRatingCount' =>  $result[0]->rating_count ?? null,
            'gameID' =>  $result[0]->id,
            'gameCover' =>  IGDBUtils::image_url($result[0]->cover->image_id, '1080p'),
            'gameDate' =>  $result[0]->release_dates[0]->human,
            'gameSummary' =>  $result[0]->summary,
            'gameGenre' =>  $result[0]->genres[0]->name,
            'loggedInUser' => $_SESSION['loggedInUser'],
        ];
        displayTemplate('review/createReview.html', $data);
    }

    public function registerReview()
    {
        $this->authorizeUser();
        $currentUser = R::findOne('users', 'id=?', [$_SESSION['loggedInUser']]);


        // Replace "`" with " " in the reviewText
        $reviewText = str_replace("`", " ", $_POST['reviewText']);
        $review = R::dispense('reviews');
        $review->username = $currentUser->username;
        $review->game_id = $_POST['game_id'];
        $review->game_title = $_POST['game_title'];
        $review->review_title = $_POST['reviewTitle'];
        $review->review = $reviewText;
        $review->rating = $_POST['rating'];
        $review->created_at = new DateTime('now');
        $review->updated_at = new DateTime('now');

        R::store($review);
        redirect('/review/show?id=' . $review->id);

        die();
    }

    public function editReview()
    {
        $this->authorizeUser();
        $review = $this->getBeanById('reviews', $_GET['id']);

        $gameID = R::getCell('SELECT game_id FROM reviews WHERE id = ?', [$_GET['id']]);
        $username = R::getCell('SELECT username FROM reviews WHERE id = ?', [$_GET['id']]);
        $loggedinUsername = R::getCell('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);


        if ($username != $loggedinUsername) {
            error(301, 'You aren\'t authorized to edit this review. ' . $_GET['id']);
        }

        // instantiating the wrapper
        $igdb = new IGDB("k0c3065725802ija8esaw24v3tlh8h", "fta0z7aobuzq6n6eeobii4ilrn6iru");
        $builder = new IGDBQueryBuilder();

        $result = $igdb->game(
            $builder
                ->name("Game with ID of " . $gameID)
                ->fields("id, name, release_dates.human, rating_count, release_dates.date, release_dates.y, genres.name, rating, summary, cover.*")
                ->where("id = " . $gameID)
                ->build()
        );

        $data = [
            'gameName' =>  $result[0]->name,
            'gameRating' =>  $result[0]->rating ?? null,
            'gameRatingCount' =>  $result[0]->rating_count ?? null,
            'gameID' =>  $gameID,
            'gameCover' =>  IGDBUtils::image_url($result[0]->cover->image_id, '1080p'),
            'gameDate' =>  $result[0]->release_dates[0]->human,
            'gameSummary' =>  $result[0]->summary,
            'gameGenre' =>  $result[0]->genres[0]->name,
            'reviews' => $reviews ?? null,
            'id' => $_GET['id'],
            'review' => $review,
            'result' => $result,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,

        ];

        displayTemplate('review/editReview.html', $data);
    }

    public function editReviewPost()
    {
        $this->authorizeUser();
        $currentUser = R::findOne('users', 'id=?', [$_SESSION['loggedInUser']]);
        $oldRating = R::getCell('SELECT rating FROM reviews WHERE id=?', [$_POST['id']]);

        if (empty($_POST['rating'])) {
            $rating = $oldRating;
        } else {
            $rating = $_POST['rating'];
        }

        $reviewTxt = str_replace("`", " ", $_POST['reviewText']);

        $review = R::dispense('reviews');
        $review->id = $_POST['id'];
        $review->username = $currentUser->username;
        $review->review_title = $_POST['reviewTitle'];
        $review->review = $reviewTxt;
        $review->rating = $rating;
        $review->updated_at = new DateTime('now');
        $review->created_at = $_POST['created_at'];

        R::store($review);
        header("Location: /review/show?id=" . $_POST['id']);
        die();
    }

    public function delete()
    {

        $this->authorizeUser();
        if (empty($_GET['id'])) {
            error(404, 'No review ID given');
        }
        $review = $this->getBeanById('reviews', $_GET['id']);
        if (is_null($review)) {
            error(404, 'No review available with ID: ' . $_GET['id']);
        }
        R::trash($review);

        header("Location: /profile/me");
        die();
    }

    public function gamePicker()
    {
        $this->authorizeUser();
        // instantiating the wrapper
        $igdb = new IGDB("k0c3065725802ija8esaw24v3tlh8h", "fta0z7aobuzq6n6eeobii4ilrn6iru");
        $builder = new IGDBQueryBuilder();

        if (!empty($_POST['title'])) {
            try {
                $result = $igdb->game(
                    $builder
                        ->fields("id, name, release_dates.human, genres.name, cover.*")
                        ->search($_POST['title'])
                        ->limit(16)
                        ->build()
                );
            } catch (IGDBInvalidParameterException $e) {
                // invalid parameter passed to the builder
                echo $e->getMessage();
            } catch (IGDBEndpointException $e) {
                // failed query
                echo $e->getMessage();
            }

            $noResults = false;
            if (empty($result)) {
                $noResults = true;
            }

            $data = [
                'games' => $result,
                'reviews' => $reviews ?? null,
                'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
                'noResults' => $noResults,
            ];
        } else {
            $data = [
                'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            ];
        }

        displayTemplate('review/gamePicker.html', $data);
    }
}
