<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
</head>

<body>
    <h1 class="course-page-title">Courses</h1>
    <div class="bar">
        <form method="get" action="/courses" id="search-form">
            <input type="hidden" name="page" value="1">
            <input type="search" placeholder="Search..." id="query" name="query" value="<?= $query ?>">
            <input type="hidden" name="sort" value="<?= $sortColumn ?>">
            <input type="hidden" name="order" value="<?= $sortOrder ?>">
        </form>
        <div class="sorting">
            <span>Sort by: </span>
            <a class="<?= $sortColumn === 'title' ? 'current' : '' ?>"
                href="?page=<?= $currentPage ?>&query=<?= $query ?>&sort=title&order=<?= $sortOrder === 'asc' ? 'desc' : 'asc' ?>&show_enrolled=<?= $enrolled ?>">Title
                <?= $sortColumn === 'title' ? ($sortOrder === 'asc' ? '▾' : '▴') : '' ?></a>
            <a class="<?= $sortColumn === 'popularity' ? 'current' : '' ?>"
                href="?page=<?= $currentPage ?>&query=<?= $query ?>&sort=popularity&order=<?= $sortOrder === 'asc' ? 'desc' : 'asc' ?>&show_enrolled=<?= $enrolled ?>"">Popularity <?= $sortColumn === 'popularity' ? ($sortOrder === 'asc' ? '▾' : '▴') : '' ?></a>
        <a class=" <?= $sortColumn === 'created_at' ? 'current' : '' ?>"
                href="?page=<?= $currentPage ?>&query=<?= $query ?>&sort=created_at&order=<?= $sortOrder === 'asc' ? 'desc' : 'asc' ?>&show_enrolled=<?= $enrolled ?>"">Date <?= $sortColumn === 'created_at' ? ($sortOrder === 'asc' ? '▾' : '▴') : '' ?></a>
    </div>
</div>
<?php if ($courses): ?>
    <div class=" courses-container">
                    <?php foreach ($courses as $key => $course): ?>
                        <div class="course-container">
                            <h3><?= $course['id'] . ': ' . $course['title'] ?></h3>
                            <p><?= $course['description'] ?></p>
                            <div class="view-course-container">
                                <a class="view-course"
                                    href="/courses/<?= $course['id'] ?>/<?= str_replace(' ', '-', $course['title']) ?>">View
                                    Course</a>
                                <?php if (!empty($submitted[$key])): ?>
                                    <div class="succes-small flex"><span>✅ Submitted!</span></div>
                                <?php endif ?>
                            </div>
                            <!-- REPLACE STRING OF COURSE TITLE TO INCLUD '-'. SERVERS HANDLE IT BETTER THIS WAY. NOT GOOD WITH SPACES ' ' -->
                        </div>
                    <?php endforeach ?>
            </div>

            <div class="pagination">
                <div>
                    <a class="p-button <?= (int) $currentPage === 1 ? 'disabled' : '' ?>"
                        href="?page=<?= $currentPage - 1 ?>&query=<?= $query ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>&show_enrolled=<?= $enrolled ?>">Previous</a>
                </div>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <div>
                        <a class="<?= $i === $currentPage ? 'current currentb' : '' ?> p-numbers"
                            href="?page=<?= $i ?>&query=<?= $query ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>&show_enrolled=<?= $enrolled ?>"><?= $i ?></a>
                    </div>
                <?php endfor ?>
                <div>
                    <a class="p-button <?= (int) $currentPage === (int) $totalPages ? 'disabled' : '' ?>"
                        href="?page=<?= $currentPage + 1 ?>&query=<?= $query ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>&show_enrolled=<?= $enrolled ?>">Next</a>
                </div>
            </div>
        <?php else: ?>
            <div class="no-results">No results found.</div>
        <?php endif ?>
        <script src="<?= JS_DIR ?>"></script>
</body>

</html>