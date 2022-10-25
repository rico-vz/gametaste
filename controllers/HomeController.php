<?php

use RedBeanPHP\R as R;

class HomeController extends BaseController
{
    public function index()
    {
        $igdb = new IGDB("UR_API_KEY", "UR_API_KEY");

        $builder = new IGDBQueryBuilder();
        $result = $igdb->game(
            $builder
                ->fields("id, name, release_dates.human, rating_count, release_dates.date, release_dates.y, genres.name, summary, cover.*")
                ->custom_where("(rating_count >= 22 & first_release_date >= 1640995261)")
                ->limit(20)
                ->sort(array(
                    "field" => "rating",
                    "direction" => "desc"
                ))
                ->build()
        );

        $randNum = rand(0, count($result) - 1);

        $data = [
            'gameOneName' =>  $result[0]->name,
            'gameOneID' =>  $result[0]->id,
            'gameTwoName' =>  $result[1]->name,
            'gameTwoID' =>  $result[1]->id,
            'gameThreeName' =>  $result[2]->name,
            'gameThreeID' =>  $result[2]->id,
            'gameFourName' =>  $result[3]->name,
            'gameFourID' =>  $result[3]->id,
            'gameOneDate' =>  $result[0]->release_dates[0]->human,
            'gameTwoDate' =>  $result[1]->release_dates[0]->human,
            'gameThreeDate' =>  $result[2]->release_dates[0]->human,
            'gameFourDate' =>  $result[3]->release_dates[0]->human,
            'gameOneCover' => IGDBUtils::image_url($result[0]->cover->image_id, '1080p'),
            'gameTwoCover' => IGDBUtils::image_url($result[1]->cover->image_id, '1080p'),
            'gameThreeCover' => IGDBUtils::image_url($result[2]->cover->image_id, '1080p'),
            'gameFourCover' => IGDBUtils::image_url($result[3]->cover->image_id, '1080p'),
            'testCover' => 'https://images.weserv.nl/?url=' . IGDBUtils::image_url($result[$randNum]->cover->image_id, '1080p_2x') . '&w=800&h=1200&fit=cover',
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
        ];
        displayTemplate('home/index.html', $data);
    }

    public function profile()
    {
        $this->authorizeUser();
        $pastes = R::getAll('SELECT * FROM pastes WHERE user_id = ?', [$_SESSION['loggedInUser']]);
        $username = R::getAll('SELECT username FROM users WHERE id = ?', [$_SESSION['loggedInUser']]);
        $data = [
            'pastes' => $pastes,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            'username' => $username[0]['username'],
        ];
        displayTemplate('paste/profile.html', $data);
    }

    public function show()
    {
        if (empty($_GET['id'])) {
            error(404, 'No paste ID given');
        }
        $paste = $this->getBeanById('pastes', $_GET['id']);
        if (is_null($paste)) {
            error(404, 'No paste available with ID: ' . $_GET['id']);
        }
        displayTemplate('paste/show.html', [
            'paste' => $paste,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
            'id' => $_GET['id'],
        ]);
    }

    public function create()
    {
        $this->authorizeUser();
        //shows form to create a new paste
        $data = [
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
        ];
        displayTemplate('paste/create.html', $data);
    }

    public function createPost()
    {
        $this->authorizeUser();
        //Enter new paste into database
        $paste = R::dispense('pastes');
        $paste->title = $_POST['title'];
        $paste->content = $_POST['content'];
        $paste->user_id = $_SESSION['loggedInUser'];
        $paste->type = $_POST['language'];
        $paste->date = date('Y-m-d');
        R::store($paste);
        header("Location: /paste/profile");
        die();
    }

    public function delete()
    {
        $this->authorizeUser();
        if (empty($_GET['id'])) {
            error(404, 'No paste ID given');
        }
        $paste = $this->getBeanById('pastes', $_GET['id']);
        if (is_null($paste)) {
            error(404, 'No paste available with ID: ' . $_GET['id']);
        }
        if ($paste->user_id != $_SESSION['loggedInUser']) {
            error(403, 'You are not allowed to delete this paste');
        }
        R::trash($paste);
        header("Location: /paste/profile");
        die();
    }

    public function termsOfService()
    {
        $data = [
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
        ];
        displayTemplate('home/termsOfService.html', $data);
    }

    public function privacyPolicy()
    {
        displayTemplate('home/privacyPolicy.html', []);
    }

    public function games()
    {
        $igdb = new IGDB("k0c3065725802ija8esaw24v3tlh8h", "fta0z7aobuzq6n6eeobii4ilrn6iru");

        $builder = new IGDBQueryBuilder();
        $result = $igdb->game(
            $builder
                ->fields("id, name, release_dates.human, rating_count, release_dates.date, release_dates.y, genres.name, summary, cover.*")
                ->custom_where("(rating_count >= 9 & first_release_date >= 1614070769)")
                ->limit(120)
                ->sort(array(
                    "field" => "first_release_date",
                    "direction" => "desc"
                ))
                ->build()
        );

        $data = [
            'games' => $result,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
        ];

        displayTemplate('home/gamefeed.html', $data);
    }

    public function feed()
    {
        $allReviews = R::getAll('SELECT * FROM reviews');
        $data = [
            'reviews' => $allReviews,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
        ];

        displayTemplate('home/reviewfeed.html', $data);
    }

    public function gameSearch()
    {
        $igdb = new IGDB("k0c3065725802ija8esaw24v3tlh8h", "fta0z7aobuzq6n6eeobii4ilrn6iru");

        $builder = new IGDBQueryBuilder();
        $result = $igdb->game(
            $builder
                ->fields("id, name, release_dates.human, rating_count, release_dates.date, release_dates.y, genres.name, summary, cover.*")
                ->search($_POST['gameTitle'])
                ->limit(60)
                ->build()
        );

        $data = [
            'games' => $result,
            'loggedInUser' => $_SESSION['loggedInUser'] ?? null,
        ];

        displayTemplate('home/gamefeed.html', $data);
    }
}