<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Simple Music Player</title>
    <!-- Load FontAwesome icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"
    />

    <!-- Load the custom CSS style file -->
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css" />
  </head>
  <body>
    <div class="player">
      <!-- Define the section for displaying details -->
      <div class="details">
        <div class="now-playing">PLAYING x OF y</div>
        <div class="track-art"></div>
        <div class="track-name">Track Name</div>
        <div class="track-artist">Track Artist</div>
      </div>

      <!-- Define the section for displaying track buttons -->
      <div class="buttons">
        <div class="prev-track" onclick="prevTrack()">
          <i class="fa fa-step-backward fa-2x"></i>
        </div>
        <div class="playpause-track" onclick="playpauseTrack()">
          <i class="fa fa-play-circle fa-5x"></i>
        </div>
        <div class="next-track" onclick="nextTrack()">
          <i class="fa fa-step-forward fa-2x"></i>
        </div>
      </div>

      <!-- Define the section for displaying the seek slider-->
      <div class="slider_container">
        <div class="current-time">00:00</div>
        <input
          type="range"
          min="1"
          max="100"
          value="0"
          class="seek_slider"
          onchange="seekTo()"
        />
        <div class="total-duration">00:00</div>
      </div>

      <!-- Define the section for displaying the volume slider-->
      <div class="slider_container">
        <i class="fa fa-volume-down"></i>
        <input
          type="range"
          min="1"
          max="100"
          value="99"
          class="volume_slider"
          onchange="setVolume()"
        />
        <i class="fa fa-volume-up"></i>
      </div>
    </div>

    <!-- Load the main script for the player -->
  </body>
  <script src="./assets/js/main.js"></script>
  <script>
      let track_list = [
    {
        name: "Sanam re",
        artist: "Akash",
        poster: "./assets/img/sanamre.jpg",
        song: "./assets/song/sanamre.mp3"
    },
    {
        name: "Dj Wale Babu",
        artist: "Badshah",
        poster: "./assets/img/dj.jpg",
        song: "./assets/song/dj.mp3"
    },
];
let loadSong =async  ()=>{
    let res = await fetch("http://localhost:8000/getSongs");
    let data = await res.json()
    track_list = data
    loadTrack(0);
}
loadSong()
    // Load the first track in the tracklist
    function loadTrack(track_index) {
      // Clear the previous seek timer
      clearInterval(updateTimer);
      resetValues();

      // Load a new track
      curr_track.src = track_list[track_index].song;
      curr_track.load();

      // Update details of the track
      track_art.style.backgroundImage =
        "url(" + track_list[track_index].poster + ")";
      track_name.textContent = track_list[track_index].name;
      track_artist.textContent = track_list[track_index].artist;
      now_playing.textContent =
        "PLAYING " + (track_index + 1) + " OF " + track_list.length;

      // Set an interval of 1000 milliseconds
      // for updating the seek slider
      updateTimer = setInterval(seekUpdate, 1000);

      // Move to the next track if the current finishes playing
      // using the 'ended' event
      curr_track.addEventListener("ended", nextTrack);

      // Apply a random background color
      random_bg_color();
    }

    function random_bg_color() {
      // Get a random number between 64 to 256
      // (for getting lighter colors)
      let red = Math.floor(Math.random() * 256) + 64;
      let green = Math.floor(Math.random() * 256) + 64;
      let blue = Math.floor(Math.random() * 256) + 64;

      // Construct a color withe the given values
      let bgColor = "rgb(" + red + ", " + green + ", " + blue + ")";

      // Set the background to the new color
      document.body.style.background = bgColor;
    }

    // Function to reset all values to their default
    function resetValues() {
      curr_time.textContent = "00:00";
      total_duration.textContent = "00:00";
      seek_slider.value = 0;
    }

    // play
    function playpauseTrack() {
      // Switch between playing and pausing
      // depending on the current state
      if (!isPlaying) playTrack();
      else pauseTrack();
    }

    function playTrack() {
      // Play the loaded track
      curr_track.play();
      isPlaying = true;

      // Replace icon with the pause icon
      playpause_btn.innerHTML = '<i class="fa fa-pause-circle fa-5x"></i>';
    }

    function pauseTrack() {
      // Pause the loaded track
      curr_track.pause();
      isPlaying = false;

      // Replace icon with the play icon
      playpause_btn.innerHTML = '<i class="fa fa-play-circle fa-5x"></i>';
    }

    function nextTrack() {
      // Go back to the first track if the
      // current one is the last in the track list
      if (track_index < track_list.length - 1) track_index += 1;
      else track_index = 0;

      // Load and play the new track
      loadTrack(track_index);
      playTrack();
    }

    function prevTrack() {
      // Go back to the last track if the
      // current one is the first in the track list
      if (track_index > 0) track_index -= 1;
      else track_index = track_list.length - 1;

      // Load and play the new track
      loadTrack(track_index);
      playTrack();
    }

    //  control
    function seekTo() {
      // Calculate the seek position by the
      // percentage of the seek slider
      // and get the relative duration to the track
      seekto = curr_track.duration * (seek_slider.value / 100);

      // Set the current track position to the calculated seek position
      curr_track.currentTime = seekto;
    }

    function setVolume() {
      // Set the volume according to the
      // percentage of the volume slider set
      curr_track.volume = volume_slider.value / 100;
    }

    function seekUpdate() {
      let seekPosition = 0;

      // Check if the current track duration is a legible number
      if (!isNaN(curr_track.duration)) {
        seekPosition = curr_track.currentTime * (100 / curr_track.duration);
        seek_slider.value = seekPosition;

        // Calculate the time left and the total duration
        let currentMinutes = Math.floor(curr_track.currentTime / 60);
        let currentSeconds = Math.floor(
          curr_track.currentTime - currentMinutes * 60
        );
        let durationMinutes = Math.floor(curr_track.duration / 60);
        let durationSeconds = Math.floor(
          curr_track.duration - durationMinutes * 60
        );

        // Add a zero to the single digit time values
        if (currentSeconds < 10) {
          currentSeconds = "0" + currentSeconds;
        }
        if (durationSeconds < 10) {
          durationSeconds = "0" + durationSeconds;
        }
        if (currentMinutes < 10) {
          currentMinutes = "0" + currentMinutes;
        }
        if (durationMinutes < 10) {
          durationMinutes = "0" + durationMinutes;
        }

        // Display the updated duration
        curr_time.textContent = currentMinutes + ":" + currentSeconds;
        total_duration.textContent = durationMinutes + ":" + durationSeconds;
      }
    }
  </script>
</html>