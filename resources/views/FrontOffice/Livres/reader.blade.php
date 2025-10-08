@extends('baseF')

@section('content')
<style>
:root {
  /* Thème nude clair */
  --bg: #f8f3ef;          /* fond beige rosé */
  --card: #fffaf7;        /* fond des cartes */
  --text: #3a2f2b;        /* texte brun doux */
  --accent: #d1a085;      /* accent nude rosé */
}

/* Thème nude foncé (mode sombre élégant) */
.dark-mode {
  --bg: #2b2422;
  --card: #3a2f2b;
  --text: #f7ede6;
  --accent: #e0b89d;
}
.tool-btn {
  background: var(--toolbar-bg);
  border: none;
  color: var(--accent);
  font-size: 18px;
  padding: 10px;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tool-btn:hover {

  color: #a97450ff;
}
/* CSS à ajouter dans ton <style> ou fichier existant */
.fullscreen-wrapper {
  position: fixed !important;
  inset: 0 !important;
  z-index: 9999 !important;
  background: var(--bg) !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  justify-content: center !important;
  overflow: auto !important;
}

.fullscreen-container {
  width: 100vw !important;
  height: 100vh !important;
  max-width: none !important;
  max-height: none !important;
  border-radius: 0 !important;
}

.zoom-scroll {
  overflow: auto !important;
}
#bookmarkSidebar.show {
    right: 0 !important;
}
/* Ligne marqueur */
.mark-line {
  position: absolute;
  left: 0;
  width: 100%;
  height: 3px;
  background: rgba(220,20,60,0.95);
  z-index: 2000;
  cursor: grab;
}

/* Conteneur petit bouton supprimer sur la ligne */
.mark-line .remove-btn {
  position: absolute;
  right: 6px;
  top: -10px;
  background: #fff;
  border-radius: 50%;
  padding: 2px 6px;
  font-size: 12px;
  border: 1px solid rgba(0,0,0,0.08);
  cursor: pointer;
  z-index: 2100;
}



.sidebar {
  position: fixed;
  top: 0;
  right: 0;
  width: 250px;
  height: 100%;
  background: #f7f7f7;
  box-shadow: -2px 0 8px rgba(0,0,0,0.2);
  padding: 16px;
  display: none;
}

.sidebar.active {
  display: block;
}

.sidebar .close-btn {
  background: none;
  border: none;
  color: #444;
  font-size: 20px;
  cursor: pointer;
  float: right;
}


body { margin:0; background:var(--bg); font-family: "SF Pro Display", Arial, sans-serif; color:var(--text); }

/* header (titre + compteur) */
.book-header { display:flex; flex-direction:column; align-items:center; gap:8px; margin-top:20px; }
.book-header h2 { margin:0; font-size:22px; font-weight:600; }
.page-counter { font-size:13px; background:var(--card); padding:6px 12px; border-radius:18px; box-shadow:0 4px 12px rgba(0,0,0,0.06); }

/* floating toolbar (auto-hide) */
.floating-toolbar {
  position:fixed; top:12px; left:50%; transform:translateX(-50%); padding:8px; border-radius:12px;
  display:flex; gap:8px; align-items:center; z-index:1200;
  background: rgba(255,255,255,0.46); backdrop-filter: blur(6px); box-shadow:0 6px 20px rgba(0,0,0,0.08);
  transition: opacity .35s, transform .35s;
}
.floating-toolbar.hidden { opacity:0; transform: translateX(-50%) translateY(-6px); pointer-events:none; }

/* toolbar buttons */
.tool-btn { background:transparent; border:none; padding:8px; border-radius:8px; cursor:pointer; color:var(--text); display:flex; align-items:center; justify-content:center; }
.tool-btn:hover { background: rgba(0,0,0,0.06); color:var(--accent); }

/* reader */
.readerWrapper { display:flex; justify-content:center; align-items:center; padding-bottom:90px; margin-top:8px; }
#book-container {
  width:900px; height:600px; max-width:calc(100% - 40px); max-height:80vh;
  background:var(--card); border-radius:12px; box-shadow:0 22px 48px rgba(0,0,0,0.18);
  overflow:hidden; position:relative;
  transform-origin: top center; /* important pour le zoom CSS */
}

/* when zoom > 1 we allow scrolling on parent wrapper */
.readerWrapper.zoom-scroll { overflow:auto; }

/* ensure page images fit nicely */
.pf-page img, .pf-page__content img, .page img { width:100%; height:100%; object-fit:contain; display:block; }

/* nav buttons below */
.nav-row { display:flex; justify-content:center; gap:20px; margin-top:18px; margin-bottom:34px; }
.btn-outline { text-decoration:none; display:inline-flex; gap:8px; align-items:center; padding:9px 20px; border-radius:26px; border:2px solid var(--accent); color:var(--accent); background:transparent; font-weight:600; }
.btn-outline:hover { background:var(--accent); color:#fff; transform:translateY(-2px); }

/* hidden overlay removed by default (we don't show it) */
.render-overlay { display:none !important; }

/* dark theme */
.dark-mode { --bg:#0f1113; --card:#111419; --text:#e8eef7; --accent:#3da1ff; }
</style>

<!-- HEADER -->
<div class="book-header">
  <span class="logo-text">📖{{ $title }}</span>
  <div id="pageCounter" class="page-counter">0 / 0</div>
</div>

<!-- FLOATING TOOLBAR (auto-hide) -->

<!-- LOADER -->
<div id="pdfLoader" style="
  position: fixed;
  inset: 0;
  background: rgba(255, 244, 240, 0.95);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  color: #d1a085;
  font-family: 'SF Pro Display', sans-serif;
  z-index: 2000;
">
  Please be patient... Loading your book 📖
</div>
<div id="fullscreenWrapper">
<!-- READER -->
<div class="readerWrapper" id="readerWrapper">
  <div id="book-container"></div>
</div>
<!-- SIDEBAR BOOKMARK -->
<!-- SIDEBAR BOOKMARK (remplacer l'ancien bloc) -->
<div id="bookmarkSidebar" style="
    position: fixed;
    top: 0;
    right: -320px; /* cachée par défaut */
    width: 320px;
    height: 100%;
    background: var(--card);
    box-shadow: -4px 0 12px rgba(0,0,0,0.2);
    transition: right 0.3s ease;
    z-index: 1500;
    padding: 16px;
    overflow-y: auto;
">
  <div style="display:flex; align-items:center; justify-content:space-between;">
    <h3 style="margin:0;">Mark page</h3>
    <button id="closeSidebar" class="tool-btn" title="Close" style="font-size:20px; line-height:1; background:transparent; border:none;">&times;</button>
  </div>

  <hr style="margin:12px 0;">
  <p>Page: <strong><span id="sidebarPage">0</span></strong></p>
  <p>Position: <strong><span id="sidebarLine">0</span></strong> px</p>

  <button id="markLineBtn" class="tool-btn" style="color: #d1a085; margin-top:10px;">
    <i class="fa-solid fa-pen"></i> Mark Ligne
  </button>

  <h4 style="margin-top:18px;">My markers</h4>
  <div id="marksList" style="margin-top:8px;"></div>
</div>


<!-- NAV BUTTONS -->
<div class="nav-row">
  <a href="#" id="prevPage" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Previous Page</a>
  <a href="#" id="nextPage" class="btn-outline">Next Page <i class="fa-solid fa-arrow-right"></i></a>
</div>

<div id="floatingToolbar" class="floating-toolbar">
  <button id="btnZoomOut" style="color: #d1a085;" class="tool-btn" title="Zoom out"><i class="fa-solid fa-magnifying-glass-minus"></i></button>
  <button id="btnZoomIn" style="color: #d1a085;" class="tool-btn" title="Zoom in"><i class="fa-solid fa-magnifying-glass-plus"></i></button>
  <button id="btnFullscreen" style="color: #d1a085;" class="tool-btn" title="Fullscreen"><i class="fa-solid fa-expand"></i></button>
  <button id="btnTheme" style="color: #d1a085;"  class="tool-btn" title="Toggle theme"><i id="themeIcon" class="fa-solid fa-moon"></i></button>
  <button id="btnSound" style="color: #d1a085;" class="tool-btn" title="Toggle page sound"><i id="soundIcon" class="fa-solid fa-volume-high"></i></button>
  <button id="btnBookmark" style="color: #d1a085;" class="tool-btn" title="Mark the page"><i id="bookmarkIcon" class="fa-regular fa-bookmark"></i></button>
</div>
</div>
<!-- ICONS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

<!-- PDF.js + worker -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
<script>pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';</script>

<!-- PageFlip -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/page-flip/dist/css/page-flip.min.css">
<script src="https://cdn.jsdelivr.net/npm/page-flip/dist/js/page-flip.browser.min.js"></script>

<script>
 document.addEventListener('DOMContentLoaded', async () => {
    // show loader once at page load
 const loader = document.getElementById('pdfLoader');

  // Blade -> JS safe
  const pdfUrl = @json($pdfUrl);
  const userName = @json(auth()->user()->email ?? 'USER');

const bookId = encodeURIComponent(pdfUrl); // identifiant pour bookmark
let currentPage = 0; // page actuelle, globale

 const btnBookmark = document.getElementById('btnBookmark');
const bookmarkIcon = document.getElementById('bookmarkIcon');

const bookUrl = @json($pdfUrl); // blade
  // DOM refs
  const container = document.getElementById('book-container');
  const wrapper = document.getElementById('readerWrapper');
  const pageCounter = document.getElementById('pageCounter');
  const floatingToolbar = document.getElementById('floatingToolbar');
  const btnZoomIn = document.getElementById('btnZoomIn');
  const btnZoomOut = document.getElementById('btnZoomOut');
  const btnFullscreen = document.getElementById('btnFullscreen');
  const btnTheme = document.getElementById('btnTheme');
  const themeIcon = document.getElementById('themeIcon');
  const btnSound = document.getElementById('btnSound');
  const soundIcon = document.getElementById('soundIcon');
  const btnNext = document.getElementById('nextPage');
  const btnPrev = document.getElementById('prevPage');
const sidebar = document.getElementById('bookmarkSidebar');
const sidebarPage = document.getElementById('sidebarPage');
const sidebarLine = document.getElementById('sidebarLine');
const markLineBtn = document.getElementById('markLineBtn');
const closeSidebar = document.getElementById('closeSidebar');
const readerWrapper = document.getElementById('readerWrapper');
const marksList = document.getElementById('marksList');

btnBookmark.addEventListener('click', async () => {
    const currentPage = pageFlip.getCurrentPageIndex();
    const scrollY = wrapper.scrollTop;

    // Afficher la sidebar
    sidebar.classList.add('show');

    // Marqueur horizontal existant ?
    const markY = currentMark ? parseFloat(currentMark.style.top) : null;
    const markX = currentXMark ? parseFloat(currentXMark.style.left) : null;

    // Mettre à jour la sidebar
    sidebarPage.textContent = currentPage + 1;
    sidebarLine.textContent = markY !== null ? markY : 0;

    // Changer l'icône
    bookmarkIcon.classList.replace('fa-regular', 'fa-solid');

    try {
        const res = await fetch('/bookmark/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                book_url: bookUrl, 
                page: currentPage, 
                scroll_y: scrollY,
                mark_y: markY,
                mark_x: markX
            })
        });
        const data = await res.json();
        if (!data.success) console.warn('Impossible de sauvegarder le bookmark');
    } catch(e) {
        console.error('Bookmark save failed', e);
    }
});


    let currentMarkY = null;
    let currentMarkX = null;
    let isDraggingY = false;
    let startY = 0;
    let isDraggingX = false;
    let startX = 0;
const EDGE_OFFSET = 10;

function createMarkLine(initialTop) {
    const mark = document.createElement('div');
    mark.classList.add('mark-line');
    mark.style.position = 'absolute';
    mark.style.top = `${initialTop}px`;
    mark.style.left = `${EDGE_OFFSET}px`;
    mark.style.width = `calc(100% - ${EDGE_OFFSET*2}px)`;
    mark.style.height = '3px';
    mark.style.background = 'red';
    mark.style.cursor = 'grab';
    readerWrapper.appendChild(mark);

    mark.addEventListener('mousedown', e => {
        e.preventDefault();
        const startY = e.clientY - mark.getBoundingClientRect().top;

        function onMouseMove(eMove) {
            let newTop = eMove.clientY - readerWrapper.getBoundingClientRect().top - startY;
            newTop = Math.max(EDGE_OFFSET, Math.min(readerWrapper.clientHeight - EDGE_OFFSET, newTop));
            mark.style.top = `${newTop}px`;
            sidebarLine.textContent = Math.round(newTop);
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
    });
}

     const btnReset = document.createElement('button');
    btnReset.textContent = 'Reset Marker';
    btnReset.classList.add('tool-btn');
    btnReset.style.color = '#FF6347';
    btnReset.style.marginTop = '10px';
    sidebar.insertBefore(btnReset, marksList);

    btnReset.addEventListener('click', () => {
        if (currentMarkY) currentMarkY.remove();
        if (currentMarkX) currentMarkX.remove();
        currentMarkY = null;
        currentMarkX = null;
        // reset sidebar display
        sidebarPage.textContent = '0';
        sidebarLine.textContent = '0';
        // reset in DB
        fetch('/bookmark/save-line', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ book_url: bookUrl, scroll_x: 0, scroll_y: 0 })
        });
    });
// Ouvrir sidebar et ajouter une ligne
  // --- Marker Y (horizontal line) ---
    markLineBtn.addEventListener('click', () => {
        if (currentMarkY) return;
        const mark = document.createElement('div');
        mark.classList.add('mark-line');
        mark.style.top = readerWrapper.scrollTop + 'px';

        mark.addEventListener('mousedown', e => {
            isDraggingY = true;
            startY = e.clientY - mark.getBoundingClientRect().top;
            document.body.style.userSelect = 'none';
        });

        document.addEventListener('mousemove', e => {
            if (!isDraggingY) return;
            const y = e.clientY - readerWrapper.getBoundingClientRect().top - startY;
            mark.style.top = `${y}px`;
            sidebarLine.textContent = Math.round(y);
        });

        document.addEventListener('mouseup', () => {
            if (!isDraggingY) return;
            isDraggingY = false;
            document.body.style.userSelect = '';
            saveMarkPosition();
        });

        readerWrapper.appendChild(mark);
        currentMarkY = mark;
        sidebar.classList.add('show');
    });

    function saveMarkPosition() {
        const scrollY = currentMarkY ? parseFloat(currentMarkY.style.top) : 0;
        const scrollX = currentMarkX ? parseFloat(currentMarkX.style.left) : 0;

        sidebarLine.textContent = Math.round(scrollY);
        fetch('/bookmark/save-line', {
            method: 'POST',
            headers: {'Content-Type':'application/json'},
            body: JSON.stringify({ book_url: bookUrl, scroll_x: scrollX, scroll_y: scrollY })
        });
    }

    // --- Marker X (vertical line) ---
    const markXBtn = document.createElement('button');
    markXBtn.textContent = 'Mark Colonne';
    markXBtn.classList.add('tool-btn');
    markXBtn.style.color = '#1E90FF';
    markXBtn.style.marginTop = '10px';
    sidebar.insertBefore(markXBtn, marksList);

    markXBtn.addEventListener('click', () => {
        if (currentMarkX) return;
        const markX = document.createElement('div');
        markX.classList.add('mark-line');
        markX.style.width = '3px';
        markX.style.height = '100%';
        markX.style.background = 'rgba(30,144,255,0.95)';
        markX.style.top = '0px';
        markX.style.left = readerWrapper.scrollLeft + 'px';
        markX.style.cursor = 'ew-resize';

        markX.addEventListener('mousedown', e => {
            isDraggingX = true;
            startX = e.clientX - markX.getBoundingClientRect().left;
            document.body.style.userSelect = 'none';
        });

        document.addEventListener('mousemove', e => {
            if (!isDraggingX) return;
            const x = e.clientX - readerWrapper.getBoundingClientRect().left - startX;
            markX.style.left = `${x}px`;
        });

        document.addEventListener('mouseup', () => {
            if (!isDraggingX) return;
            isDraggingX = false;
            document.body.style.userSelect = '';
            saveMarkPosition();
        });

        readerWrapper.appendChild(markX);
        currentMarkX = markX;
        sidebar.classList.add('show');
    });

    // --- Charger position sauvegardée ---
    async function loadSavedMarkers() {
        try {
            const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
            const data = await res.json();
            if (!data) return;

            // position Y
            if (data.scroll_y != null) {
                const mark = document.createElement('div');
                mark.classList.add('mark-line');
                mark.style.top = data.scroll_y + 'px';
                readerWrapper.appendChild(mark);
                currentMarkY = mark;
                sidebarLine.textContent = Math.round(data.scroll_y);
            }

            // position X
            if (data.scroll_x != null) {
                const markX = document.createElement('div');
                markX.classList.add('mark-line');
                markX.style.width = '3px';
                markX.style.height = '100%';
                markX.style.background = 'rgba(30,144,255,0.95)';
                markX.style.top = '0px';
                markX.style.left = data.scroll_x + 'px';
                readerWrapper.appendChild(markX);
                currentMarkX = markX;
            }
        } catch(err) {
            console.error('Erreur chargement markers', err);
        }
    }

    loadSavedMarkers();
// Charger la position X enregistrée
async function showSavedXMarker() {
    try {
        const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
        const data = await res.json();

        if (data && data.scroll_x != null) {
            if (!document.getElementById('xPositionIcon')) {
                const iconX = document.createElement('div');
                iconX.id = 'xPositionIcon';
                iconX.textContent = '📍';
                iconX.style.position = 'absolute';
                iconX.style.top = '0px';
                iconX.style.left = (data.scroll_x || 0) + 'px';
                iconX.style.fontSize = '24px';
                iconX.style.zIndex = 1500;
                readerWrapper.appendChild(iconX);
            }
        }
    } catch(err) {
        console.error('Erreur chargement X position:', err);
    }
}

// Appeler après init
showSavedXMarker();

  // disable right-click & common shortcuts (basic deterrent)
  /*document.addEventListener('contextmenu', e => e.preventDefault());
  document.addEventListener('keydown', e => {
    if (e.ctrlKey && ['s','u','c','p'].includes(e.key.toLowerCase())) e.preventDefault();
  });
*/
 
  const pageAudio = new Audio("{{ asset('assets/video/flipAudio.m4a') }}");
pageAudio.volume = 0.4; // ajuster le volume
  let soundEnabled = true;

  // prepare pageFlip sizes based on container
  function computePageSize() {
    // We'll create one-page size roughly half container width for two-page spread look
    const contW = Math.max(600, Math.min(1200, container.clientWidth));
    const pageW = Math.floor(contW / 2) - 6;
    const pageH = Math.max(420, Math.floor(container.clientHeight) - 12);
    return { pageW, pageH };
  }
 startPage = 0;
 try {
const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
 const data = await res.json();
    if (data && data.page != null) {
        startPage = data.page; // récupère la page sauvegardée
        console.log(data.page);
    }
} catch(e) {
    console.warn('Bookmark load failed', e);
}
  // create PageFlip instance
  const { pageW, pageH } = computePageSize();
let pageFlip = new St.PageFlip(container, {
    width: pageW,
    height: pageH,
    size: 'fixed',
    showCover: true,
    drawShadow: true,
    flippingTime: 500,
     startPage: startPage
});

if (startPage > 0) {
    bookmarkIcon.classList.replace('fa-regular', 'fa-solid');
}

  // render PDF -> images (once)
  let pdfDoc = null;
  let totalPages = 0;
  const images = [];
  try {
    const loadingTask = pdfjsLib.getDocument(pdfUrl);
    pdfDoc = await loadingTask.promise;
    totalPages = pdfDoc.numPages;

    for (let i = 1; i <= totalPages; i++) {
      const page = await pdfDoc.getPage(i);
      const viewport = page.getViewport({ scale: 1.5 }); // good base resolution
      const canvas = document.createElement('canvas');
      canvas.width = viewport.width;
      canvas.height = viewport.height;
      const ctx = canvas.getContext('2d');
      await page.render({ canvasContext: ctx, viewport }).promise;

      // watermark (center, rotated)
      ctx.save();
      ctx.globalAlpha = 0.18;
      ctx.fillStyle = '#ff0000';
      ctx.textAlign = 'center';
      const fontSize = Math.max(22, Math.round(24 * 1.0));
      ctx.font = `${fontSize}px Arial`;
      ctx.translate(canvas.width/2, canvas.height/2);
      ctx.rotate(-0.35);
      ctx.fillText(userName, 0, 0);
      ctx.restore();

      images.push(canvas.toDataURL('image/png'));
    }
  } catch (err) {
    console.error('PDF render error', err);
    alert('Erreur lors du chargement du PDF. Vérifie l’URL ou la console.');
    return;
  }

  // load images into PageFlip and wait for init event
  let initDone = false;
  pageFlip.loadFromImages(images);
  await new Promise(resolve => {
pageFlip.on('init', () => {
    const total = pageFlip.getPageCount();
    pageCounter.textContent = `${startPage+1} / ${total}`;

    // flip vers la page sauvegardée
    if(startPage > 0) pageFlip.flipToPage(startPage);

    updateNavButtons();
});


    // fallback: resolve after 1s if init never fires
    setTimeout(() => { if (!initDone) resolve(); }, 1200);
  });

  // helper to update counter
  function updateCounter() {
    try {
      const cur = (typeof pageFlip.getCurrentPageIndex === 'function') ? pageFlip.getCurrentPageIndex() : 0;
      pageCounter.textContent = `${cur + 1} / ${totalPages}`;
    } catch {
      pageCounter.textContent = `1 / ${totalPages}`;
    }
  }


  // ensure button handlers set AFTER pageFlip initialized
  btnNext.addEventListener('click', (e) => { e.preventDefault(); try { pageFlip.flipNext(); } catch(err){ console.warn(err); } });
  btnPrev.addEventListener('click', (e) => { e.preventDefault(); try { pageFlip.flipPrev(); } catch(err){ console.warn(err); } });

  pageFlip.on('flip', () => { updateCounter(); if (soundEnabled) pageAudio.play().catch(()=>{}); });
  pageFlip.on('init', () => { updateCounter();});

// ------------------ SHOW/HIDE PREV & NEXT BUTTONS ------------------
 function updateNavButtons() {
    const currentPage = pageFlip.getCurrentPageIndex(); // index courant
    const total = pageFlip.getPageCount();

    if (btnPrev) btnPrev.style.display = (currentPage > 0) ? 'inline-flex' : 'none';
    if (btnNext) btnNext.style.display = (currentPage < total - 2) ? 'inline-flex' : 'none';
}

// update à chaque flip et après l’init
pageFlip.on('flip', async () => {
    updateCounter();
    updateNavButtons();

    // sauvegarde le bookmark
    const current = pageFlip.getCurrentPageIndex();
    try {
     await fetch(`/bookmark/save/${encodeURIComponent(bookUrl)}`, {
    method: 'POST',
    body: JSON.stringify({ page: currentPage }),
    headers: { 'Content-Type': 'application/json' }
});


    } catch(e){ console.warn('Impossible de sauvegarder bookmark', e); }
});

pageFlip.on('init', updateNavButtons,updateCounter());

// initial call
updateNavButtons();
loader.style.display='none';
// hide loader after first load
 if (loader) loader.style.display = 'none';

  /* ------------------ ZOOM (CSS-based, instant) ------------------ */
  let zoom = 1.0;
  function applyZoom() {
    // Apply transform to the container (scale) - transform origin top center for natural zoom
    container.style.transform = `scale(${zoom})`;
    container.style.transformOrigin = 'top center';

    // if zoom > 1 allow wrapper scrolling (so user can scroll to see full page)
    if (zoom > 1) wrapper.classList.add('zoom-scroll'); else wrapper.classList.remove('zoom-scroll');
  }

  btnZoomIn.addEventListener('click', (e) => {
    e.preventDefault();
    zoom = Math.min(2.4, +(zoom + 0.2).toFixed(2));
    applyZoom();
  });
  btnZoomOut.addEventListener('click', (e) => {
    e.preventDefault();
    zoom = Math.max(0.6, +(zoom - 0.2).toFixed(2));
    applyZoom();
  });

  /* ------------------ FULLSCREEN ------------------ */
    btnFullscreen.addEventListener('click', async () => {
    const fullscreenWrapper = document.getElementById('fullscreenWrapper');
    const navbar = document.querySelector('nav');
    const footer = document.querySelector('footer');

    try {
        if (!document.fullscreenElement) {
        // Masquer la navbar et le footer
        if (navbar) navbar.style.display = 'none';
        if (footer) footer.style.display = 'none';

        // Plein écran du livre + toolbar
        await fullscreenWrapper.requestFullscreen();

        fullscreenWrapper.style.position = 'fixed';
        fullscreenWrapper.style.inset = '0';
        fullscreenWrapper.style.zIndex = '9999';
        fullscreenWrapper.style.background = 'var(--bg)';
        fullscreenWrapper.style.display = 'flex';
        fullscreenWrapper.style.flexDirection = 'column';
        fullscreenWrapper.style.alignItems = 'center';
        fullscreenWrapper.style.justifyContent = 'center';
        fullscreenWrapper.style.overflow = 'auto'; // scroll si zoom > 1

        // Adapter le livre à l’écran
        container.style.width = '100vw';
        container.style.height = '100vh';
        container.style.maxWidth = 'none';
        container.style.maxHeight = 'none';
        container.style.borderRadius = '0';

        // Autoriser le scroll quand zoomé
        wrapper.classList.add('zoom-scroll');

        // Ajuster la mise à jour de taille PageFlip
        pageFlip.updateFromOptions({
            width: window.innerWidth,
            height: window.innerHeight
        });

        } else {
        await document.exitFullscreen();

        // Rétablir tout
        fullscreenWrapper.removeAttribute('style');
        container.removeAttribute('style');
        wrapper.classList.remove('zoom-scroll');

        // Restaurer navbar et footer
        if (navbar) navbar.style.display = '';
        if (footer) footer.style.display = '';

        // Revenir à la taille initiale du livre
        pageFlip.updateFromOptions({
            width: 1100,  // remets ici ta taille initiale
            height: 800
        });
        }
    } catch (err) {
        console.warn('Fullscreen failed', err);
    }
    });

    // When leaving fullscreen (via ESC), restore container sizes
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) {
        container.style.width = '';
        container.style.height = '';
        container.style.borderRadius = '';
        wrapper.classList.remove('zoom-scroll');
       
          btnFullscreen.innerHTML = '<i class="fa-solid fa-expand"></i>';
        }
        else{
      btnFullscreen.innerHTML = '<i class="fa-solid fa-compress"></i>';
        }
    });

  /* ------------------ THEME & SOUND ------------------ */
  btnTheme.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    themeIcon.classList.toggle('fa-moon', !isDark);
    themeIcon.classList.toggle('fa-sun', isDark);
    
  });
  btnSound.addEventListener('click', () => {
      soundEnabled = !soundEnabled;
      soundIcon.classList.toggle('fa-volume-xmark', !soundEnabled);
      soundIcon.classList.toggle('fa-volume-high', soundEnabled);
  });

  pageFlip.on('flip', () => { 
      updateCounter();
    updateNavButtons();
      if (soundEnabled) {
          // Recrée un nouvel objet audio à chaque flip pour qu'il ne continue pas
          const flipAudio = new Audio("{{ asset('assets/video/flipAudio.m4a') }}");
          flipAudio.volume = 0.4;
          flipAudio.play().catch(() => {});
      }
  });



  /* ------------------ FLOATING TOOLBAR AUTO-HIDE ------------------ */
  const toolbar = floatingToolbar;
  let toolbarTimer = null;
  function showToolbar() {
    toolbar.classList.remove('hidden');
    if (toolbarTimer) clearTimeout(toolbarTimer);
    toolbarTimer = setTimeout(() => toolbar.classList.add('hidden'), 2500);
  }
  // show toolbar on move/touch
  container.addEventListener('mousemove', showToolbar);
  container.addEventListener('touchstart', showToolbar);
  // also show on nav buttons hover
  btnNext.addEventListener('mouseenter', showToolbar);
  btnPrev.addEventListener('mouseenter', showToolbar);
  // initial hide after a short time
  toolbarTimer = setTimeout(() => toolbar.classList.add('hidden'), 1600);

  /* ------------------ RESIZE handling (keep layout stable) ------------------ */
  let resizeTimer = null;
  window.addEventListener('resize', () => {
    if (resizeTimer) clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      // On resize we don't re-render images; keep the existing pageFlip.
      // But we can try to force a simple visual reflow by toggling a trivial style:
      container.style.willChange = 'auto';
      setTimeout(()=>container.style.willChange = '', 200);
    }, 300);
  });

  // expose helpers for debugging from console
  window._bookshare_pageFlip = pageFlip;
  window._bookshare_images = images;
  window._bookshare_reloadZoom = (z) => { zoom = z || zoom; applyZoom(); updateCounter(); };

  // initial apply zoom (1.0 default)
  applyZoom();
 

// charger le marque-page au démarrage
// Charger le bookmark une fois PageFlip est prêt
async function loadBookmark() {
    try {
const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
        const data = await res.json();
        if (data && data.page != null) {
          requestAnimationFrame(() => {
    pageFlip.flipToPage(data.page);
    wrapper.scrollTop = data.scroll_y || 0;
    bookmarkIcon.classList.replace('fa-regular', 'fa-solid');
});

        }
    } catch(e) {
        console.warn('Bookmark load failed', e);
    }
}
async function showSavedPositionIcon() {
    try {
        const res = await fetch(`/bookmark/load?book_url=${encodeURIComponent(bookUrl)}`);
        const data = await res.json();

        if (data) {
            // Scroll exact
            readerWrapper.scrollLeft = data.scroll_x || 0;
            readerWrapper.scrollTop = data.scroll_y || 0;

            // Ajouter l'icône 📍
            if (!document.getElementById('positionIcon')) {
                const icon = document.createElement('div');
                icon.id = 'positionIcon';
                icon.textContent = '📍';
                icon.style.position = 'absolute';
                icon.style.top = (data.scroll_y || 0) + 'px';
                icon.style.left = (data.scroll_x || 0) + 'px';
                icon.style.fontSize = '24px';
                icon.style.zIndex = 1500;
                readerWrapper.appendChild(icon);
            }
        }
    } catch (err) {
        console.error('Erreur lors du chargement de la position:', err);
    }
}

// Appeler après le chargement du PDF et init PageFlip
showSavedPositionIcon();


});

</script>
@endsection
