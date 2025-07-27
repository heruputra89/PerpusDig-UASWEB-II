<?php
include 'config/controller.php';
require_once 'config/session_check.php';

// Get book and reading progress data
$id_buku = $_GET['id'] ?? null;
$id_user = $_SESSION['user_id'];

if (!$id_buku) {
    header('Location: buku.php');
    exit;
}

// Get book details
$book_query = "SELECT b.*
               FROM buku b 
               WHERE b.id_buku = '$id_buku'";
$book = select($book_query)[0];

// Get current reading progress
$last_page = $book['last_page'] ?? 1;
?>

<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['judul']) ?> - PDF Reader</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        :root {
            --bg-primary: #0A0A0A;
            --bg-secondary: #1A1A1A;
            --text-primary: #FFFFFF;
            --text-secondary: #A3A3A3;
            --accent-primary: #22C55E;
            --accent-secondary: #16A34A;
            --border-color: rgba(255, 255, 255, 0.1);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .reader-header {
            background: var(--bg-secondary);
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .reader-title {
            font-size: 1.2rem;
            font-weight: 500;
            margin: 0;
        }

        .reader-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .page-info {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .btn-control {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-control:hover {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .btn-control:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pdf-container {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            background: var(--bg-secondary);
            scroll-behavior: smooth;
            padding: 20px;
            min-height: calc(100vh - 80px);
        }

        #pdf-viewer {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            padding: 20px 0;
        }

        .pdf-page {
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            width: 100%;
            max-width: 800px;
            display: flex;
            justify-content: center;
            margin: 0 auto;
        }

        .pdf-page canvas {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .magnifier {
            position: absolute;
            width: 200px;
            height: 200px;
            border: 2px solid var(--accent-primary);
            border-radius: 50%;
            pointer-events: none;
            display: none;
            background-repeat: no-repeat;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .zoom-controls {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
            background: var(--bg-primary);
            padding: 10px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .zoom-btn {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .zoom-btn:hover {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .zoom-level {
            color: var(--text-primary);
            padding: 0 10px;
            display: flex;
            align-items: center;
            font-size: 0.9rem;
        }

        .page-controls {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 1000;
            background: var(--bg-primary);
            padding: 10px 20px;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .page-btn {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .page-btn:hover {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        .page-info {
            color: var(--text-primary);
            padding: 8px 16px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--accent-primary);
        }

        .page-jump {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--bg-primary);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            display: none;
        }

        .page-jump input {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 8px 12px;
            border-radius: 5px;
            width: 100px;
            margin-right: 10px;
        }

        .page-jump button {
            background: var(--accent-primary);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .page-jump button:hover {
            background: var(--accent-secondary);
        }
    </style>
</head>
<body>
    <div class="reader-header">
        <h1 class="reader-title"><?= htmlspecialchars($book['judul']) ?></h1>
        <div class="reader-controls">
            <button class="btn-control" id="close-reader">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>

    <div class="pdf-container" id="pdf-container">
        <div id="pdf-viewer"></div>
        <div class="magnifier" id="magnifier"></div>
    </div>

    <div class="page-controls">
        <button class="page-btn" id="prev-page">
            <i class="bi bi-chevron-left"></i> Sebelumnya
        </button>
        <span class="page-info">Halaman <span id="current-page">1</span> dari <span id="total-pages">0</span></span>
        <button class="page-btn" id="next-page">
            Selanjutnya <i class="bi bi-chevron-right"></i>
        </button>
        <button class="page-btn" id="jump-page">
            <i class="bi bi-search"></i> Lompat ke Halaman
        </button>
    </div>

    <div class="page-jump" id="page-jump">
        <input type="number" id="page-number" min="1" placeholder="Nomor halaman">
        <button id="jump-button">Lompat</button>
    </div>

    <div class="zoom-controls">
        <button class="zoom-btn" id="zoom-out">
            <i class="bi bi-dash-lg"></i>
        </button>
        <span class="zoom-level"><span id="zoom-level">100</span>%</span>
        <button class="zoom-btn" id="zoom-in">
            <i class="bi bi-plus-lg"></i>
        </button>
    </div>

    <script>
        // Initialize PDF.js
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let currentPage = <?= $last_page ?>;
        let totalPages = 0;
        let scale = 1.0;
        let isMagnifierActive = false;
        const viewer = document.getElementById('pdf-viewer');
        const container = document.getElementById('pdf-container');
        let pageElements = [];
        let saveTimeout;

        // Load the PDF
        async function loadPDF() {
            try {
                const pdfUrl = 'uploads/books/<?= $book['pdf_file'] ?>';
                
                const loadingTask = pdfjsLib.getDocument(pdfUrl);
                const pdf = await loadingTask.promise;
                
                pdfDoc = pdf;
                totalPages = pdf.numPages;
                document.getElementById('total-pages').textContent = totalPages;
                
                // Set initial scale
                scale = 1.0;
                
                // Render all pages
                await renderAllPages();
            } catch (error) {
                console.error('Error loading PDF:', error);
                alert('Error loading PDF. Please try again.');
            }
        }

        // Render all pages
        async function renderAllPages() {
            try {
                // Clear previous content
                viewer.innerHTML = '';
                pageElements = [];

                // Render all pages
                for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                    const page = await pdfDoc.getPage(pageNum);
                    
                    const pageDiv = document.createElement('div');
                    pageDiv.className = 'pdf-page';
                    pageDiv.id = `page-${pageNum}`;

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');

                    // Calculate scale to fit width while maintaining aspect ratio
                    const containerWidth = viewer.clientWidth;
                    const viewport = page.getViewport({ scale: 1.0 });
                    const scale = Math.min(containerWidth / viewport.width, 1.2);
                    const scaledViewport = page.getViewport({ scale });

                    // Set canvas dimensions
                    canvas.width = scaledViewport.width;
                    canvas.height = scaledViewport.height;

                    pageDiv.appendChild(canvas);
                    viewer.appendChild(pageDiv);
                    pageElements.push(pageDiv);

                    // Render PDF page
                    await page.render({
                        canvasContext: context,
                        viewport: scaledViewport
                    }).promise;
                }

                // Update current page based on scroll position
                updateCurrentPage();
            } catch (error) {
                console.error('Error rendering pages:', error);
                alert('Error rendering PDF pages. Please try again.');
            }
        }

        // Update current page based on scroll position
        function updateCurrentPage() {
            const container = document.getElementById('pdf-container');
            const pages = document.querySelectorAll('.pdf-page');
            const containerTop = container.scrollTop;
            const containerHeight = container.clientHeight;
            const containerMiddle = containerTop + containerHeight / 2;

            let currentPage = 1;
            pages.forEach((page, index) => {
                const pageTop = page.offsetTop;
                const pageBottom = pageTop + page.offsetHeight;
                if (containerMiddle >= pageTop && containerMiddle <= pageBottom) {
                    currentPage = index + 1;
                }
            });

            document.getElementById('current-page').textContent = currentPage;
            saveProgress(currentPage);
        }

        // Handle scroll events
        document.getElementById('pdf-container').addEventListener('scroll', () => {
            updateCurrentPage();
        });

        // Handle page navigation
        document.getElementById('prev-page').addEventListener('click', () => {
            const container = document.getElementById('pdf-container');
            const currentPage = parseInt(document.getElementById('current-page').textContent);
            if (currentPage > 1) {
                const prevPage = document.getElementById(`page-${currentPage - 1}`);
                prevPage.scrollIntoView({ behavior: 'smooth' });
            }
        });

        document.getElementById('next-page').addEventListener('click', () => {
            const container = document.getElementById('pdf-container');
            const currentPage = parseInt(document.getElementById('current-page').textContent);
            if (currentPage < totalPages) {
                const nextPage = document.getElementById(`page-${currentPage + 1}`);
                nextPage.scrollIntoView({ behavior: 'smooth' });
            }
        });

        // Handle zoom controls
        document.getElementById('zoom-in').addEventListener('click', () => {
            scale = Math.min(scale + 0.2, 2.0);
            updateZoom();
        });

        document.getElementById('zoom-out').addEventListener('click', () => {
            scale = Math.max(scale - 0.2, 0.5);
            updateZoom();
        });

        function updateZoom() {
            document.getElementById('zoom-level').textContent = Math.round(scale * 100);
            renderAllPages();
        }

        // Enhanced magnifier functionality
        let magnifierScale = 2;
        container.addEventListener('mousemove', (e) => {
            if (!isMagnifierActive) return;

            const rect = container.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            // Update magnifier position
            magnifier.style.left = `${e.clientX - 100}px`;
            magnifier.style.top = `${e.clientY - 100}px`;

            // Update magnifier content
            const canvas = pageElements[0].querySelector('canvas');
            if (canvas) {
                magnifier.style.backgroundImage = `url(${canvas.toDataURL()})`;
                magnifier.style.backgroundSize = `${canvas.width * magnifierScale}px ${canvas.height * magnifierScale}px`;
                magnifier.style.backgroundPosition = `-${x * magnifierScale - 100}px -${y * magnifierScale - 100}px`;
            }
        });

        // Toggle magnifier with double click
        container.addEventListener('dblclick', () => {
            isMagnifierActive = !isMagnifierActive;
            magnifier.style.display = isMagnifierActive ? 'block' : 'none';
        });

        // Adjust magnifier scale with mouse wheel
        container.addEventListener('wheel', (e) => {
            if (!isMagnifierActive) return;
            e.preventDefault();
            
            if (e.deltaY < 0) {
                magnifierScale = Math.min(magnifierScale + 0.5, 4);
            } else {
                magnifierScale = Math.max(magnifierScale - 0.5, 1);
            }
        });

        // Page jump functionality
        const pageJump = document.getElementById('page-jump');
        const jumpPageBtn = document.getElementById('jump-page');
        const jumpButton = document.getElementById('jump-button');
        const pageNumberInput = document.getElementById('page-number');

        jumpPageBtn.addEventListener('click', () => {
            pageJump.style.display = 'block';
            pageNumberInput.focus();
        });

        jumpButton.addEventListener('click', () => {
            const pageNum = parseInt(pageNumberInput.value);
            if (pageNum >= 1 && pageNum <= totalPages) {
                const targetPage = document.getElementById(`page-${pageNum}`);
                if (targetPage) {
                    targetPage.scrollIntoView({ behavior: 'smooth' });
                    document.getElementById('current-page').textContent = pageNum;
                    saveProgress(pageNum);
                }
                pageJump.style.display = 'none';
                pageNumberInput.value = '';
            } else {
                alert('Nomor halaman tidak valid!');
            }
        });

        // Close page jump when clicking outside
        document.addEventListener('click', (e) => {
            if (!pageJump.contains(e.target) && e.target !== jumpPageBtn) {
                pageJump.style.display = 'none';
            }
        });

        // Handle Enter key in page jump input
        pageNumberInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                jumpButton.click();
            }
        });

        // Save reading progress
        function saveProgress(pageNum) {
            if (saveTimeout) {
                clearTimeout(saveTimeout);
            }

            saveTimeout = setTimeout(() => {
                fetch('buku.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `update_progress=1&id_buku=<?= $id_buku ?>&page=${pageNum}`
                });
            }, 1000);
        }

        // Close reader
        document.getElementById('close-reader').addEventListener('click', () => {
            if (currentPage === totalPages) {
                fetch('buku.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `finish_reading=1&id_buku=<?= $id_buku ?>`
                });
            }
            window.close();
        });

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            const container = document.getElementById('pdf-container');
            const currentPage = parseInt(document.getElementById('current-page').textContent);
            
            if (e.key === 'ArrowUp' && currentPage > 1) {
                const prevPage = document.getElementById(`page-${currentPage - 1}`);
                prevPage.scrollIntoView({ behavior: 'smooth' });
            } else if (e.key === 'ArrowDown' && currentPage < totalPages) {
                const nextPage = document.getElementById(`page-${currentPage + 1}`);
                nextPage.scrollIntoView({ behavior: 'smooth' });
            } else if (e.key === '+') {
                scale = Math.min(scale + 0.2, 2.0);
                updateZoom();
            } else if (e.key === '-') {
                scale = Math.max(scale - 0.2, 0.5);
                updateZoom();
            }
        });

        // Load PDF when page loads
        window.addEventListener('load', loadPDF);
    </script>
</body>
</html> 