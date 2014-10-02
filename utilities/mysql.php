<?php
  require_once 'constants.php';
  session_start();

  class Mysql {

    private $mysqli;
    private $conn;
     
     // basically constructor
    function __construct() {
      $this->mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME) OR DIE ("Unable to 
        connect to database! Please try again later.");
      if (mysqli_connect_errno()) {
        printf("Can't connect to MySQL Server. Error code: %s\n", mysqli_connect_error());
        return null;
      }
    }
    
    // for escaping sql injections
    function quote_smart($value) {
      if (get_magic_quotes_gpc()) {
        $value = stripslashes($value);
      }
    
      if (!is_numeric($value)) {
        $value = $this->mysqli->real_escape_string($value);
      }
      return $value;
    }

    function translateWeekday($weekday) {
      switch($weekday){ 
        case 1: return "Sun"; 
        case 2: return "Mon";
        case 3: return "Tue";
        case 4: return "Wed";
        case 5: return "Thu";
        case 6: return "Fri";
        case 7: return "Sat"; 
      }
    }

    function getAvailableGuides($weekday, $time, $numTours) {
      $day = $this->translateWeekday($weekday);
      $sql = "SELECT availability.username from (availability inner join (SELECT COUNT(*) cnt, tours.username FROM tours where date='$date' and time='$time' GROUP BY username) t on availability.username = t.username) where weekday='$day' and time='$time' and cnt=$numTours";
      $result = $this->mysqli->query($sql) or die("get available guides");
      return $result;
    }

    function getMinimumScheduledTours($date, $time) {
      $sql = "SELECT MIN(cnt) FROM (SELECT COUNT(*) cnt FROM tours where date='$date' and time='$time' GROUP BY username) t";
      $result = $this->mysqli->query($sql) or die("get min scheduled tours");
      $result_array = $result->fetch_array(MYSQLI_NUM);
      return $result_array[0];
    }
  }
?>