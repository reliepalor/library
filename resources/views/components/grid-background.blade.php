    <style>
        :root{
    /* tweak these to match the image */
    --bg: #ffffff;           /* page background */
    --grid-color: #efefef;   /* faint grid line color */
    --grid-size: 48px;       /* size of each square */
    --line-thickness: .5px;   /* thickness of grid lines */
    --header-blue: #2f82c9;  /* blue top line color */
    --header-thin: #cfe3f6;  /* faint thin line under blue */
    --header-height: 3px;    /* blue line height */
  }

  html,body {height:100%; margin:0;}
  body {
    background-color: var(--bg);
    /* two gradients: vertical and horizontal lines */
    background-image:
      linear-gradient(to right, var(--grid-color) var(--line-thickness), transparent calc(var(--line-thickness))),
      linear-gradient(to bottom, var(--grid-color) var(--line-thickness), transparent calc(var(--line-thickness)));
    background-size: var(--grid-size) var(--grid-size);
    /* make lines crisp on some zoom levels */
    background-repeat: repeat;
    position: relative;
    font-family: sans-serif;
  }

  /* top header line like in your screenshot */
  .top-rule {
    position: fixed;             /* stays at top of viewport */
    left: 0;
    right: 0;
    top: 0;
    height: calc(var(--header-height) + 1px); /* blue + thin below */
    pointer-events: none;
    z-index: 9999;
  }
  .top-rule::before{
    content: "";
    display:block;
    height: var(--header-height);
    background: var(--header-blue);
  }
  .top-rule::after{
    content: "";
    display:block;
    height: 1px;
    background: var(--header-thin);
  }

  /* optional container to demonstrate content spacing */
  .page {
    padding: 28px;
  }
    </style>
  <div class="page">