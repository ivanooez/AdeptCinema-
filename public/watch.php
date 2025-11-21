<?php
require_once __DIR__ . '/../common/config.php';
session_start();
if (!isset($_SESSION['user_id'])) { header('Location:/login.php'); exit; }

$movie_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$movie_id) { header('Location:/'); exit; }

$stmt = $conn->prepare("SELECT id,title,description FROM movies WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $movie_id);
$stmt->execute();
$movie = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$movie) { header('Location:/'); exit; }
$page_title = 'Watch: ' . $movie['title'];
require_once __DIR__ . '/../common/header.php';
?>
<div class="p-4">
  <div id="player" class="bg-black rounded overflow-hidden">
    <div id="videoWrapper" style="position:relative;padding-top:56.25%;background:#000"></div>
    <div id="loading" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#06b6d4">
      <i class="fas fa-spinner fa-spin fa-2x"></i>
    </div>
    <div id="error" style="display:none;padding:20px;color:#f87171"></div>
  </div>

  <h1 class="mt-4 text-2xl"><?php echo htmlspecialchars($movie['title']); ?></h1>
  <p class="text-gray-300 mt-2"><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>
</div>

<script>
const movieId = <?php echo $movie_id; ?>;
const wrapper = document.getElementById('videoWrapper');
const loading = document.getElementById('loading');
const errorBox = document.getElementById('error');

function showError(msg){
  loading.style.display='none';
  errorBox.style.display='block';
  errorBox.textContent = msg;
}

fetch('/get_video_url.php?movie_id=' + movieId)
  .then(r => r.json())
  .then(data => {
    if (!data.success) return showError(data.error || 'Failed to fetch video');
    if (data.type === 'google_drive_embed') {
      const iframe = document.createElement('iframe');
      iframe.src = data.url;
      iframe.style.position='absolute'; iframe.style.top=0; iframe.style.left=0;
      iframe.style.width='100%'; iframe.style.height='100%'; iframe.allow='autoplay; encrypted-media';
      wrapper.appendChild(iframe);
      loading.style.display='none';
      return;
    }
    // use <video> for direct or google_drive_direct
    const video = document.createElement('video');
    video.controls = true; video.playsInline = true; video.style.width='100%'; video.style.height='100%';
    const source = document.createElement('source');
    source.src = data.url;
    source.type = 'video/mp4';
    video.appendChild(source);
    if (data.subtitle_url) {
      const track = document.createElement('track');
      track.kind='subtitles'; track.label='Default'; track.srclang='bg'; track.src = data.subtitle_url; track.default = true;
      video.appendChild(track);
      track.addEventListener('load', ()=> {
        setTimeout(()=> {
          try {
            const tt = Array.from(video.textTracks).find(t => t.label === 'Default' || t.language === 'bg');
            if (tt) tt.mode = 'showing';
          } catch(e){ console.warn(e) }
        }, 200);
      });
    }
    video.addEventListener('loadedmetadata', ()=> loading.style.display='none');
    video.addEventListener('error', ()=> showError('Video failed to load. The source may be blocked.'));
    video.style.position='absolute'; video.style.top=0; video.style.left=0; video.style.width='100%'; video.style.height='100%';
    wrapper.appendChild(video);
  })
  .catch(err => showError('Network error: ' + err.message));
</script>

<?php require_once __DIR__ . '/../common/bottom.php'; ?>
