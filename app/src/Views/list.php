<header class="bg-white shadow">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Projektlista</h1>
    </div>
</header>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <div class="ml-auto max-w-lg">
            <form id="filterForm" method="GET" action="/" class="flex justify-end mb-4">
                <select id="statusFilter" name="status" class="block rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-gray-400 sm:max-w-xs sm:text-sm sm:leading-6 mr-2 py-1 px-3">
                    <option value="all">Összes</option>
                    <?php if (isset($statuses)) : ?>
                        <?php foreach ($statuses as $status) : ?>
                            <option value="<?= $status->key ?>"><?= $status->name ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-600 rounded py-2 px-4 transition duration-200"><i class="fas fa-filter"></i></button>
            </form>
        </div>
        <?php if (!empty($projects)) : ?>
            <ul class="space-y-4">
                <?php foreach ($projects as $project) : ?>
                    <li class="project bg-white rounded-lg shadow px-5 py-4 grid gap-4">
                        <div class="grid grid-cols-2 items-center">
                            <h2 class="text-lg font-semibold">
                                <a href="/show?id=<?= $project->id ?>" class="text-black hover:text-black"><?= $project->title ?></a>
                            </h2>
                            <div class="text-right">
                                <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded <?= $project->statusKey === 'todo' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : ($project->statusKey === 'in_progress' ? 'bg-blue-100 text-blue-800 border border-blue-400' : 'bg-green-100 text-green-800 border border-green-400') ?>">
                                    <?= $project->statusName ?>
                                </span>
                            </div>
                        </div>
                        <div>
                            <p><?= $project->ownerName ?></p>
                            <p class="text-sm text-gray-500"><?= $project->ownerEmail ?></p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="/edit?id=<?= $project->id ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-600 rounded py-2 px-4 transition duration-200 inline-block text-center"><i class="fas fa-edit"></i></a>
                            <a href="/delete?id=<?= $project->id ?>" class="delete-project bg-gray-200 hover:bg-gray-300 text-gray-600 rounded py-2 px-4 transition duration-200 inline-block text-center"><i class="fas fa-trash-alt"></i></a>
                            <a href="/show?id=<?= $project->id ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-600 rounded py-2 px-4 transition duration-200 inline-block text-center"><i class="fas fa-info-circle"></i></a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php
            $currentPage = $_GET['page'] ?? 1;
            $previousPage = $currentPage > 1 ? $currentPage - 1 : 1;
            $nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
            $status = $_GET['status'] ?? '';
            ?>
            <div class="grid grid-cols-3 items-center my-4 min-h-[50px]">
                <div></div>
                <div class="pagination flex justify-center space-x-4">
                    <?php if ($totalPages > 1) : ?>
                        <a href="<?= $status !== null && $status !== '' ? "?status=" . $status . "&" : "?" ?>page=<?= $previousPage ?>" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200 <?= $currentPage > 1 ? 'hover:bg-gray-600' : 'opacity-50 pointer-events-none' ?>"><i class="fas fa-chevron-left"></i></a>
                        <a href="<?= $status !== null && $status !== '' ? "?status=" . $status . "&" : "?" ?>page=<?= $nextPage ?>" class="bg-gray-400 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-200 <?= $currentPage < $totalPages ? 'hover:bg-gray-600' : 'opacity-50 pointer-events-none' ?>"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <div class="text-right p-1">
                    Összesen: <?= $projectCount ?>
                </div>
            </div>
        <?php else : ?>
            <div class="bg-gray-200 border border-gray-300 p-1 px-2 rounded">Nem találtunk projektet.</div>
        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.getElementById('filterForm').addEventListener('submit', function(event) {
            if (document.getElementById('statusFilter').value === 'all') {
                event.preventDefault();
                window.location.href = '/';
            }
        });

        var urlParams = new URLSearchParams(window.location.search);
        var status = urlParams.get('status');
        if (status !== null && status !== '') {
            document.getElementById('statusFilter').value = status;
        } else {
            document.getElementById('statusFilter').value = 'all';
        }

        document.querySelectorAll('.delete-project').forEach(function(link) {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                var deleteUrl = this.getAttribute('href');

                Swal.fire({
                    title: 'Biztos vagy benne, hogy törölni akarod a projektet?',
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: 'Igen',
                    confirmButtonColor: '#dd6b55',
                    denyButtonText: 'Nem',
                    cancelButtonText: 'Mégse',
                    focusCancel: true,
                    icon: 'warning',
                    customClass: {
                        cancelButton: 'order-1 right-gap',
                        confirmButton: 'order-2',
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(deleteUrl, {
                                method: 'GET',
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(data.message, '', 'success')
                                        .then((value) => {
                                            link.closest('.project').remove();
                                        });
                                } else {
                                    Swal.fire(data.message, '', 'error');
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                                toastr.error('An error occurred', event);
                            });
                    }
                })
            });
        });
    });
</script>