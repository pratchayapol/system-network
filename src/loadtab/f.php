<script>
  var progressBar = document.getElementById('progress-bar');

  // เมื่อเว็บโหลดเสร็จสิ้น
  window.addEventListener('load', function() {
    // เปลี่ยนความกว้างของแถบเป็น 100% เพื่อซ่อนแถบสีโหลด
    progressBar.style.width = '100%';

    // หลังจากซ่อนแถบสีโหลดเสร็จ ซ่อนแถบสีโหลดทั้งหมด
    setTimeout(function() {
      var loadingBar = document.getElementById('loading-bar');
      loadingBar.style.display = 'none';
    }, 300); // 0.3 วินาที
  });

  // อัพเดตความกว้างของแถบสีโหลดเมื่อโหลดเว็บ
  function updateProgressBar() {
    var totalHeight = document.documentElement.scrollHeight - window.innerHeight;
    var progress = (window.pageYOffset / totalHeight) * 100;
    progressBar.style.width = progress + '%';
  }

  // เมื่อมีการเลื่อนหน้าเว็บ
  window.addEventListener('scroll', updateProgressBar);
  window.addEventListener('resize', updateProgressBar);
</script>