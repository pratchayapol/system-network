<div id="loading-bar">
  <div id="progress-bar"></div>
</div>
<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300&display=swap" rel="stylesheet">
<style>
  #loading-bar {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background-color: #ccc;
    z-index: 9999;
  }

  #progress-bar {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg, red, orange, yellow, green, blue, indigo, violet);
    background-size: 400% 100%;
    animation: rainbow 3s linear infinite;
    transition: width 0.3s ease-out;
  }

  @keyframes rainbow {
    0% { background-position: 0% 50%; }
    100% { background-position: 100% 50%; }
  }

  .t1 {
    font-family: 'Noto Sans Thai', sans-serif;
  }
</style>
