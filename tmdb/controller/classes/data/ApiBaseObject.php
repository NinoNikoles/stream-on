<?php

/**
 * 	This class is the base class for all data all the data you can get from a Movie
 *
 *	@package TMDB-V3-PHP-API
 * 	@author Bogdan Finn | <a href="https://twitter.com/BogdanFinn">Twitter</a>
 * 	@version 0.1
 * 	@date 14/09/2017
 * 	@link https://github.com/pixelead0/tmdb_v3-PHP-API-
 * 	@copyright Licensed under BSD (http://www.opensource.org/licenses/bsd-license.php)
 */
class ApiBaseObject
{
    //------------------------------------------------------------------------------
    // Class Constants
    //------------------------------------------------------------------------------

    const MEDIA_TYPE_MOVIE = 'movie';
    const CREDITS_TYPE_CAST = 'cast';
    const CREDITS_TYPE_CREW = 'crew';
    const MEDIA_TYPE_TV = 'tv';

    //------------------------------------------------------------------------------
    // Class Variables
    //------------------------------------------------------------------------------

    protected $_data;

    /**
     * 	Construct Class
     *
     * 	@param array $data An array with the data of the ApiObject
     */
    public function __construct($data) {
        $this->_data = $data;
    }

    /**
     * 	Get the ApiObject id
     *
     * 	@return int
     */
    public function getID() {
        return $this->_data['id'];
    }

    /**
     * 	Get the ApiObject Poster
     *
     * 	@return string
     */
    public function getPoster() {
        return $this->_data['poster_path'];
    }

    public function getPosters() {
        $mainPoster = $this->getPoster();
        $images = array();
        array_push($images, $mainPoster);
        
        $posters = $this->getImages();
        $posters = $posters['posters'];
        foreach ( $posters as $poster ) {
            if(!in_array($poster['file_path'], $images)) {
                array_push($images, $poster['file_path']);
            }
        }
       
        return $images;
    }

     /** 
     *  Get the Collection's backdrop
     *
     *  @return string
     */
    public function getBackdrop() {
        return $this->_data['backdrop_path'];
    }

     /** 
     *  Get the Collection's backdrop
     *
     *  @return array
     */
    public function getBackdrops() {
        $mainBackdrop = $this->getBackdrop();
        $images = array();
        array_push($images, $mainBackdrop);
        
        $backdrops = $this->getImages();
        $backdrops = $backdrops['backdrops'];
        foreach ( $backdrops as $backdrop ) {
            if(!in_array($backdrop['file_path'], $images)) {
                array_push($images, $backdrop['file_path']);
            }
        }
       
        return $images;
    }

    /**
     * 	Get the ApiObjects vote average
     *
     * 	@return int
     */
    public function getVoteAverage() {
        return $this->_data['vote_average'];
    }

    public function getReleaseDate() {
        return $this->_data['release_date'];
    }

    public function getRuntime() {
        return $this->_data['runtime'];
    }

    /**
     * 	Get the ApiObjects vote count
     *
     * 	@return int
     */
    public function getVoteCount() {
        return $this->_data['vote_count'];
    }

    /**
     * Get the ApiObjects Cast
     * @return array of Person
     */
    public function getCast(){
        return $this->getCredits(self::CREDITS_TYPE_CAST);
    }

    /**
     * Get the Cast or the Crew of an ApiObject
     * @param string $key
     * @return array of Person
     */
    protected function getCredits($key){
        $persons = [];

        foreach ($this->_data['credits'][$key] as $data) {
            $persons[] = new Person($data);
        }

        return $persons;
    }

	/** 
	 * 	Get the ApiObject's genres
	 *
	 * 	@return Genre[]
	 */
	public function getGenres() {
		$genres = array();

		foreach ($this->_data['genres'] as $data) {
			$genres[] = new Genre($data);
		}

		return $genres;
	}

    /**
     * Get the ApiObject crew
     * @return array of Person
     */
    public function getCrew(){
        return $this->getCredits(self::CREDITS_TYPE_CREW);
    }

    /**
     *  Get Generic.<br>
     *  Get a item of the array, you should not get used to use this, better use specific get's.
     *
     * 	@param string $item The item of the $data array you want
     * 	@return array
     */
    public function get($item = ''){

        if(empty($item)){
            return $this->_data;
        }

        if(array_key_exists($item, $this->_data)){
            return $this->_data[$item];
        }

        return null;
    }
	
    /**
     * Add a magical call method to allow non defined get methods like $tmdb->getNextEpisodeToAir()
     *
     * @param $property
     * @param array $arguments
     *
     * @return array|null
     * @throws ErrorException
     */
    public function __call($property, array $arguments)
    {
        if (\strpos($property, 'get') !== 0) {
            throw new \ErrorException(\sprintf('Call to undefined method: %s::%s', __CLASS__, $property));
        }

        $property = \preg_replace_callback(
            '%[A-Z]+%',
            static function($match) {return  '_'. \strtolower($match[0]);},
            $property
        );

        $property = \str_replace('get_', '', $property);

        return $this->get($property);
    }
}
