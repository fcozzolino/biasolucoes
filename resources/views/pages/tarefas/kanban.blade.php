@php
  $container = 'container-xxl'; // forçado
@endphp

@extends('layouts.horizontalLayout')

@section('title', 'Tarefas | Kanban')

@section('content')

    @vite(['public/vendor/css/pages/app-kanban.css'])
    <script src="https://cdn.jsdelivr.net/npm/jkanban@1.3.1/dist/jkanban.min.js"></script>

    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            Tarefas / <span class="text-muted fw-light">{{ $board->title }}</span>
        </h4>

        <div class="app-kanban">
            <div id="kanban-wrapper"></div>
        </div>

        <!-- INICIO MODAL CARDS -->
        <div class="modal fade" id="cardModal" tabindex="-1" aria-labelledby="cardModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cardModalLabel">Detalhes do Card</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <small class="text-muted small"
                            id="card-meta-info"></small><!-- mostra nome de quem criou o card e data -->
                        <br><br>
                        <input type="hidden" id="modal-card-id">
                        @foreach (['title' => 'Título', 'description' => 'Descrição', 'full_description' => 'Descrição Completa', 'link' => 'Link', 'color' => 'Cor'] as $field => $label)
                            <div class="mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <div id="modal-{{ $field }}-display" class="editable-field border rounded p-2"
                                    data-field="{{ $field }}">
                                </div>
                            </div>
                        @endforeach

                        <!-- Upload de Anexos -->
                        <div class="mb-3">
                            <label class="form-label">Anexos</label>
                            <input type="file" id="attachment-input" multiple class="form-control" />
                            <button type="button" class="btn btn-sm btn-primary mt-2" id="upload-button">Salvar
                                Anexos</button>
                        </div>
                        <div id="upload-error" class="text-danger mt-1" style="display: none;"></div>


                        <!-- Lista de Anexos -->
                        <ul class="list-group mt-3" id="attachment-list"></ul>

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" id="btn-archive-options">
                            Arquivar
                        </button>

                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM MODAL CARD -->

        <!-- INICIO MODAL COR COLUNA -->
        <!-- Modal de Cor -->
        <div class="modal fade" id="modalColorPicker" tabindex="-1" aria-labelledby="modalColorPickerLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalColorPickerLabel">Selecionar Cor da Coluna</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="color" id="columnColorPicker" class="form-control form-control-color" value="#563d7c"
                            title="Escolha sua cor">
                        <input type="hidden" id="columnIdForColor">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="saveColumnColor">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- FIM MODAL COR COLUNA -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kanban = new jKanban({
                element: '#kanban-wrapper',
                gutter: '15px',
                widthBoard: '300px',
                responsivePercentage: false,
                dragBoards: true,
                dragendBoard(el) {
                    const columnOrder = Array.from(document.querySelectorAll('.kanban-board')).map((col,
                        index) => ({
                        id: col.getAttribute('data-id').replace('column-', ''),
                        order: index
                    }));
                    fetch('{{ route('columns.updateOrder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            columns: columnOrder
                        })
                    });
                },
                itemAddOptions: {
                    enabled: true,
                    content: '+ Adicionar novo cartão',
                    class: 'kanban-title-button btn btn-default border-none',
                    footer: true
                },
                dropEl(el, target) {
                    const columnId = target.closest('.kanban-board').getAttribute('data-id').replace(
                        'column-', '');
                    const cardIds = Array.from(target.children).map(c => c.getAttribute('data-eid'));
                    fetch('{{ route('cards.updateOrder') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            cards: cardIds,
                            column_id: columnId
                        })
                    });
                },
                boards: [
                    @foreach ($board->columns as $column)
                        {
                            id: 'column-{{ $column->id }}',
                            attributes: {
                                style: '--col-color: {{ $column->color ?? '#f0f0f0' }}'
                            },
                            title: `
      <div class="kanban-board-header">
      <div class="kanban-title d-flex justify-content-between align-items-center p-2">
      <span class="column-name" data-id="{{ $column->id }}">{{ $column->name }}</span>

      <div class="dropdown">
      <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
      <li>
      <a class="dropdown-item btn-change-column-color" href="#" data-id="{{ $column->id }}">Cor</a>
      </li>
      <li>
      <a class="dropdown-item btn-sort-column-cards" href="#" data-id="{{ $column->id }}">Ordenar por</a>
      </li>
      </ul>
      </div>
      </div>
      </div>
      `,
                            item: [
                                @foreach ($column->cards->sortBy('order') as $card)
                                    {
                                        id: '{{ $card->id }}',
                                        title: `
        <div class="kanban-card p-2 position-relative" data-created-at="{{ $card->created_at->timestamp }}">
        <span class="badge bg-{{ $card->color ?? 'secondary' }} position-absolute top-0 end-0 m-1">&nbsp;</span>
        <strong>{{ $card->title }}</strong>
        <div class="small text-muted">{{ $card->description }}</div>
        <div class="d-flex align-items-center gap-2 mt-2">
        @if ($card->full_description)
        <i class="fa-solid fa-align-left" title="Descrição completa disponível"></i>
      @endif
        @if ($card->attachments->count())
        <i class="fa-solid fa-paperclip" title="Anexos"></i> {{ $card->attachments->count() }}
      @endif
        </div>
        </div>`
                                    },
                                @endforeach
                            ]
                        },
                    @endforeach
                ]
            });


            @foreach ($board->columns as $column)
                (function() {
                    const boardEl = document.querySelector(`[data-id="column-{{ $column->id }}"]`);
                    if (boardEl) {
                        boardEl.style.setProperty('--col-color', '{{ $column->color ?? '#f0f0f0' }}');
                    }
                })();
            @endforeach


            // Botão "+ Nova Coluna"
            // Botão "+ Nova Coluna"
            const addColumnButton = document.createElement('button');
            addColumnButton.textContent = '+ Nova Coluna';
            addColumnButton.className = 'btn btn-success mt-4 ms-2';
            addColumnButton.onclick = () => {
                if (document.getElementById('new-column-form')) return;

                const form = document.createElement('form');
                form.id = 'new-column-form';
                form.className = 'd-flex align-items-center gap-2 mt-2';
                form.innerHTML = `
      <input type="text" name="name" class="form-control" placeholder="Nome da nova coluna" required />
      <input type="hidden" name="board_id" value="{{ $board->id }}">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <button type="button" class="btn btn-secondary">Cancelar</button>
      `;

                form.onsubmit = function(e) {
                    e.preventDefault();
                    const name = form.name.value.trim();
                    if (!name) return;

                    fetch('{{ route('columns.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                board_id: '{{ $board->id }}',
                                name
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const column = data.column;
                                const columnHTML = `
      <div class="kanban-board bg-light" data-id="column-${column.id}">
      <header class="kanban-title d-flex justify-content-between align-items-center p-2">
      <span>${column.name}</span>
      <div class="dropdown">
      <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item btn-change-column-color" href="#" data-id="${column.id}">Cor</a></li>
      </ul>
      </div>
      </header>
      <main class="kanban-drag" data-column-id="${column.id}"></main>
      </div>
      `;

                                kanban.addBoards([{
                                    id: `column-${column.id}`,
                                    attributes: {
                                        // define aqui a cor inicial da nova coluna
                                        style: `--col-color: ${column.color || '#f0f0f0'};`
                                    },
                                    title: `
      <div class="kanban-title d-flex justify-content-between align-items-center p-2">
      <span>${column.name}</span>
      <div class="dropdown">
      <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item btn-change-column-color" href="#" data-id="${column.id}">Cor</a></li>
      </ul>
      </div>
      </div>
      `,
                                    item: []
                                }]);

                                // Remove o form
                                form.remove();

                                form.remove();
                            } else {
                                alert('Erro ao criar a coluna.');
                            }
                        })
                        .catch(error => {
                            console.error('Erro:', error);
                            alert('Erro ao criar a coluna.');
                        });
                };

                form.querySelector('.btn-secondary').onclick = () => form.remove();
                addColumnButton.after(form);
            };

            document.getElementById('kanban-wrapper').after(addColumnButton);
            //FIM Botão "+ Nova Coluna"





            // Handler único de CLIQUE (delegation)
            document.addEventListener('click', function(e) {
                // 1) Abrir form de novo cartão
                if (e.target.classList.contains('kanban-title-button')) {
                    const board = e.target.closest('.kanban-board');
                    if (board.querySelector('.kanban-form')) return;
                    const form = document.createElement('form');
                    form.className = 'kanban-form p-2 bg-white border rounded mt-2';
                    form.innerHTML = `
      <input type="text" name="title" class="form-control mb-1" placeholder="Título" required />
      <textarea name="description" class="form-control mb-1" placeholder="Descrição (opcional)"></textarea>
      <div class="d-flex gap-2">
      <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
      <button type="button" class="btn btn-sm btn-secondary btn-cancel">Cancelar</button>
      </div>`;
                    board.querySelector('.kanban-drag').appendChild(form);
                    return;
                }

                // 2) Cancelar form de novo cartão ou nova coluna
                if (e.target.classList.contains('btn-cancel')) {
                    const frm = e.target.closest('form');
                    if (frm) frm.remove();
                    return;
                }

                // 3) Abrir modal de detalhes do card
                const cardEl = e.target.closest('.kanban-item');
                if (cardEl && !e.target.classList.contains('btn-delete') && e.target.id !==
                    'upload-button') {
                    const cardId = cardEl.getAttribute('data-eid');
                    fetch(`/cards/${cardId}`)
                        .then(res => res.json())
                        .then(data => {
                            document.getElementById('modal-card-id').value = data.id;
                            document.getElementById('card-meta-info').textContent =
                                `${data.user.name} adicionou este cartão em ${new Date(data.created_at).toLocaleString('pt-BR')}`;


                            ['title', 'description', 'full_description', 'link', 'color'].forEach(
                                field => {
                                    document.getElementById(`modal-${field}-display`).textContent =
                                        data[field] || '(vazio)';
                                });
                            // Lista anexos
                            const list = document.getElementById('attachment-list');
                            list.innerHTML = '';
                            (data.attachments || []).forEach(att => {
                                const li = document.createElement('li');
                                li.className =
                                    'list-group-item d-flex justify-content-between align-items-center';
                                li.innerHTML = `
      <div class="d-flex align-items-center justify-content-between w-100">
      <div class="d-flex align-items-center gap-3">
      ${att.mime_type.startsWith('image')
            ? `<img src="/storage/${att.path}" width="42" height="42" class="rounded border">`
            : `<div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                          <span class="fw-bold text-uppercase">${att.mime_type.split('/')[1]}</span>
                          </div>`}
      <div>
      <div class="fw-semibold">${att.filename}</div>
      <small class="text-muted">Adicionado em ${new Date(att.created_at).toLocaleString('pt-BR')}</small>
      </div>
      </div>
      <div class="dropdown">
      <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
      <li>
      <a class="dropdown-item text-danger btn-confirm-delete" href="#" data-id="${att.id}">Excluir</a>
      </li>
      <li><a class="dropdown-item" href="/attachments/${att.id}/download">Baixar</a></li>
      </ul>
      </div>
      </div>
      `;
                                li.id = `li-${att.id}`;
                                list.appendChild(li);
                            });
                            new bootstrap.Modal(document.getElementById('cardModal')).show();
                        });
                    return;
                }

                // 4) Inline edit (campo .editable-field)
                const editable = e.target.closest('.editable-field');
                if (editable && !editable.querySelector('input,textarea,select')) {
                    const field = editable.getAttribute('data-field');
                    const cardId = document.getElementById('modal-card-id').value;
                    const current = editable.textContent.trim();
                    let input;
                    if (field === 'color') {
                        input = document.createElement('select');
                        input.className = 'form-select';
                        ['primary', 'success', 'warning', 'danger', 'secondary'].forEach(c => {
                            const opt = document.createElement('option');
                            opt.value = c;
                            opt.textContent = c.charAt(0).toUpperCase() + c.slice(1);
                            if (c === current) opt.selected = true;
                            input.appendChild(opt);
                        });
                    } else if (['description', 'full_description'].includes(field)) {
                        input = document.createElement('textarea');
                        input.className = 'form-control';
                        input.value = current;
                    } else {
                        input = document.createElement('input');
                        input.type = 'text';
                        input.className = 'form-control';
                        input.value = current;
                    }
                    const saveBtn = document.createElement('button');
                    saveBtn.type = 'button';
                    saveBtn.className = 'btn btn-sm btn-primary mt-2 me-2';
                    saveBtn.textContent = 'Salvar';
                    const cancelBtn = document.createElement('button');
                    cancelBtn.type = 'button';
                    cancelBtn.className = 'btn btn-sm btn-secondary mt-2';
                    cancelBtn.textContent = 'Cancelar';
                    editable.innerHTML = '';
                    editable.append(input, saveBtn, cancelBtn);

                    saveBtn.addEventListener('click', () => {
                        const newVal = input.value.trim();
                        fetch(`/cards/${cardId}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    [field]: newVal
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                editable.textContent = newVal || '(vazio)';

                                const cardNode = document.querySelector(
                                    `.kanban-item[data-eid='${cardId}']`);
                                if (cardNode) {
                                    let icons = '';
                                    if (data.full_description) {
                                        icons +=
                                            `<i class="fa-solid fa-align-left" title="Descrição completa disponível"></i>`;
                                    }
                                    if (data.attachments && data.attachments.length > 0) {
                                        icons +=
                                            `<i class="fa-solid fa-paperclip" title="Anexos"></i> ${data.attachments.length}`;
                                    }

                                    cardNode.innerHTML = `
    <div class="kanban-card p-2 position-relative" data-created-at="${data.created_at}">
      <span class="badge bg-${data.color || 'secondary'} position-absolute top-0 end-0 m-1">&nbsp;</span>
      <strong>${data.title}</strong>
      <div class="small text-muted">${data.description || ''}</div>
      <div class="d-flex align-items-center gap-2 mt-2">${icons}</div>
    </div>
  `;
                                }

                            });
                    });

                    cancelBtn.addEventListener('click', () => {
                        editable.textContent = current || '(vazio)';
                    });
                    return;
                }

                // 5) Upload de anexos
                if (e.target && e.target.id === 'upload-button') {
                    const input = document.getElementById('attachment-input');
                    const cardId = document.getElementById('modal-card-id').value;
                    const files = input.files;
                    const errorDiv = document.getElementById('upload-error');
                    errorDiv.style.display = 'none';
                    errorDiv.textContent = '';

                    if (!files.length || !cardId) return;

                    Array.from(files).forEach(file => {
                        if (file.size > 10 * 1024 * 1024) {
                            errorDiv.textContent =
                                `O arquivo "${file.name}" excede o limite de 10MB.`;
                            errorDiv.style.display = 'block';
                            return;
                        }

                        const formData = new FormData();
                        formData.append('file', file);

                        fetch(`/cards/${cardId}/attachments`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: formData
                            })
                            .then(res => {
                                if (!res.ok) throw new Error('Falha no upload');
                                return res.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    const li = document.createElement('li');
                                    li.className =
                                        'list-group-item d-flex justify-content-between align-items-center';
                                    li.innerHTML = `
      <div class="d-flex align-items-center justify-content-between w-100">
      <div class="d-flex align-items-center gap-3">
      ${data.attachment.mime_type.startsWith('image')
            ? `<img src="/storage/${data.attachment.path}" width="42" height="42" class="rounded border">`
            : `<div class="bg-light border rounded d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                          <span class="fw-bold text-uppercase">${data.attachment.mime_type.split('/')[1]}</span>
                          </div>`}
      <div>
      <div class="fw-semibold">${data.attachment.filename}</div>
      <small class="text-muted">Adicionado em ${new Date(data.attachment.created_at).toLocaleString('pt-BR')}</small>
      </div>
      </div>
      <div class="dropdown">
      <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item text-danger  btn-confirm-delete" href="#" data-id="${data.attachment.id}">Excluir</a></li>
      <li><a class="dropdown-item" href="/attachments/${data.attachment.id}/download">Baixar</a></li>
      </ul>
      </div>
      </div>
      `;
                                    li.id = `li-${data.attachment.id}`;
                                    // Inicializa popover do novo botão
                                    const deleteBtn = li.querySelector(
                                        '.btn-delete[data-bs-toggle="popover"]');
                                    if (deleteBtn) {
                                        new bootstrap.Popover(deleteBtn);
                                    }
                                    document.getElementById('attachment-list').appendChild(li);
                                    input.value = '';

                                    // ATUALIZA O CARD
                                    // ATUALIZA O CARD
                                    fetch(`/cards/${cardId}`)
                                        .then(res => res.json())
                                        .then(updatedCard => {
                                            const cardEl = document.querySelector(
                                                `.kanban-item[data-eid="${cardId}"]`);
                                            if (!cardEl) return;

                                            let icons = '';
                                            if (updatedCard.full_description) {
                                                icons +=
                                                    `<i class="fa-solid fa-align-left" title="Descrição completa disponível"></i>`;
                                            }
                                            if (updatedCard.attachments && updatedCard
                                                .attachments.length > 0) {
                                                icons +=
                                                    `<i class="fa-solid fa-paperclip" title="Anexos"></i> ${updatedCard.attachments.length}`;
                                            }

                                            cardEl.innerHTML = `
      <div class="kanban-card p-2 position-relative" data-created-at="${updatedCard.created_at}">
      <span class="badge bg-${updatedCard.color || 'secondary'} position-absolute top-0 end-0 m-1">&nbsp;</span>
      <strong>${updatedCard.title}</strong>
      <div class="small text-muted">${updatedCard.description || ''}</div>
      <div class="d-flex align-items-center gap-2 mt-2">${icons}</div>
      </div>
    `;
                                        });

                                }
                            })
                            .catch(err => {
                                console.error(err);
                                errorDiv.textContent =
                                    `Erro ao enviar o arquivo "${file.name}".`;
                                errorDiv.style.display = 'block';
                            });
                    });
                }


                //Atualizar card com icones
                function atualizarCard(cardId) {
                    fetch(`/cards/${cardId}`)
                        .then(res => res.json())
                        .then(updatedCard => {
                            const cardEl = document.querySelector(`.kanban-item[data-eid="${cardId}"]`);
                            if (!cardEl) return;

                            let icons = '';
                            if (updatedCard.full_description) {
                                icons +=
                                    `<i class="fa-solid fa-align-left" title="Descrição completa disponível"></i>`;
                            }
                            if (updatedCard.attachments && updatedCard.attachments.length > 0) {
                                icons +=
                                    `<i class="fa-solid fa-paperclip" title="Anexos"></i> ${updatedCard.attachments.length}`;
                            }

                            cardEl.innerHTML = `
      <div class="kanban-card p-2 position-relative" data-created-at="${updatedCard.created_at}">
      <span class="badge bg-${updatedCard.color || 'secondary'} position-absolute top-0 end-0 m-1">&nbsp;</span>
      <strong>${updatedCard.title}</strong>
      <div class="small text-muted">${updatedCard.description || ''}</div>
      <div class="d-flex align-items-center gap-2 mt-2">${icons}</div>
      </div>
    `;
                        });
                }
                //fim atualiza card com icones


                // 6) Excluir anexo
                if (e.target.closest('.btn-delete')) {
                    e.preventDefault();

                    const btn = e.target.closest('.btn-delete');
                    const id = btn.dataset.id;
                    const cardId = document.getElementById('modal-card-id').value;

                    fetch(`/attachments/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                const dropdown = btn.closest('.dropdown');
                                const li = dropdown?.closest('li');
                                if (li) li.remove();

                                // ATUALIZA O CARD
                                fetch(`/cards/${cardId}`)
                                    .then(res => res.json())
                                    .then(updatedCard => {
                                        const cardEl = document.querySelector(
                                            `.kanban-item[data-eid="${cardId}"]`);
                                        if (!cardEl) return;

                                        let icons = '';
                                        if (data.full_description) {
                                            icons +=
                                                `<i class="fa-solid fa-align-left" title="Descrição completa disponível"></i>`;
                                        }
                                        if (data.attachments_count > 0) {
                                            icons +=
                                                `<i class="fa-solid fa-paperclip" title="Anexos"></i> ${data.attachments_count}`;
                                        }


                                        cardNode.innerHTML = `
      <div class="kanban-card p-2 position-relative" data-created-at="${data.created_at}">
      <span class="badge bg-${data.color || 'secondary'} position-absolute top-0 end-0 m-1">&nbsp;</span>
      <strong>${data.title}</strong>
      <div class="small text-muted">${data.description || ''}</div>
      <div class="d-flex align-items-center gap-2 mt-2">${icons}</div>
      </div>
      `;

                                    });
                            } else {
                                showError('Erro ao excluir o anexo.');
                            }
                        })
                        .catch(err => {
                            console.error('Erro ao excluir:', err);
                            showError('Falha ao excluir o anexo.');
                        });
                }




            });

            // Handler único de SUBMIT (novo cartão)
            document.addEventListener('submit', function(e) {
                if (!e.target.matches('.kanban-form')) return;
                e.preventDefault();
                const form = e.target;
                const columnId = form.closest('.kanban-board').getAttribute('data-id').replace('column-',
                    '');
                const title = form.title.value;
                const description = form.description.value;
                fetch('{{ route('cards.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            column_id: columnId,
                            title,
                            description
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        form.remove();
                        kanban.addElement('column-' + columnId, {
                            id: data.card.id,
                            title: `
      <div class="kanban-card p-2 position-relative" data-created-at="${data.card.created_at}">
      <span class="badge bg-${data.card.color || 'secondary'} position-absolute top-0 end-0 m-1">&nbsp;</span>
      <strong>${data.card.title}</strong>
      <div class="small text-muted">${data.card.description || ''}</div>
      </div>`
                        });
                    });
            });

            // Edição do modal (form de edição do card)
            document.getElementById('cardEditForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('modal-card-id').value;
                const payload = ['title', 'description', 'full-description', 'link', 'color']
                    .reduce((obj, field) => {
                        obj[field.replace('-', '_')] = document.getElementById(`modal-${field}`).value;
                        return obj;
                    }, {});
                fetch(`/cards/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(payload)
                    })
                    .then(res => res.json())
                    .then(data => {
                        const cardNode = document.querySelector(`.kanban-item[data-eid='${id}']`);
                        if (cardNode) {
                            cardNode.innerHTML = `
    <div class="kanban-card p-2 position-relative" data-created-at="${data.created_at}">
    <span class="badge bg-${data.color || 'secondary'} position-absolute top-0 end-0 m-1">&nbsp;</span>
    <strong>${data.title}</strong>
    <div class="small text-muted">${data.description || ''}</div>
    <div class="d-flex align-items-center gap-2 mt-2">${icons}</div>
    </div>
    `;

                        }
                        bootstrap.Modal.getInstance(document.getElementById('cardModal')).hide();
                    });
            });

        });


        //confirmação de exclusão popover
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('.btn-confirm-delete');
            if (trigger) {
                e.preventDefault();

                const attachmentId = trigger.dataset.id;

                // Fecha qualquer popover aberto
                document.querySelectorAll('.popover').forEach(p => p.remove());

                const popover = new bootstrap.Popover(trigger, {
                    placement: 'top',
                    html: true,
                    title: 'Confirmar exclusão',
                    content: '<div id="popover-confirm-content">Carregando...</div>'
                });

                popover.show();

                // Após exibir o popover, substituir o conteúdo com HTML completo
                setTimeout(() => {
                    const container = document.getElementById('popover-confirm-content');
                    if (container) {
                        container.innerHTML = `
                        <p class="mb-1"><strong>Tem certeza de que deseja excluir este anexo?</strong><br> Esta ação não pode ser desfeita.</p>
                        <button class="btn btn-sm btn-danger mt-2 btn-delete-final" data-id="${attachmentId}" data-li-id="li-${attachmentId}">
                        Sim, remover
                        </button>
                        `;
                    }
                }, 100);
            }

            // Confirmação final do delete
            const confirmBtn = e.target.closest('.btn-delete-final');
            if (confirmBtn) {
                const id = confirmBtn.dataset.id;
                const liId = confirmBtn.dataset.liId;

                fetch(`/attachments/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Remove li visualmente
                            const li = document.getElementById(liId);
                            if (li) li.remove();

                            // Fecha popover
                            document.querySelectorAll('.popover').forEach(p => p.remove());

                            // Atualiza contador de anexos no card
                            const cardId = document.getElementById('modal-card-id').value;
                            const cardEl = document.querySelector(`.kanban-item[data-eid="${cardId}"]`);
                            if (cardEl) {
                                const iconBlock = cardEl.querySelector(
                                    '.kanban-card .d-flex.align-items-center.gap-2.mt-2');
                                const paperclipIcon = iconBlock?.querySelector('.fa-paperclip');
                                const countNode = paperclipIcon?.nextSibling;

                                if (paperclipIcon && countNode && countNode.nodeType === 3) {
                                    let count = parseInt(countNode.textContent.trim()) || 1;
                                    count = Math.max(0, count - 1);

                                    if (count === 0) {
                                        paperclipIcon.remove();
                                        countNode.remove();
                                    } else {
                                        countNode.textContent = ` ${count}`;
                                    }
                                }
                            }
                        } else {
                            alert('Erro ao excluir.');
                        }
                    });
            }
        });



        // Atualizar contador de anexos no card
        const cardId = document.getElementById('modal-card-id').value;
        const cardEl = document.querySelector(`.kanban-item[data-eid="${cardId}"]`);
        if (cardEl) {
            const badge = cardEl.querySelector('.fa-paperclip')?.nextSibling;
            if (badge && badge.nodeType === 3) { // nó de texto
                const current = parseInt(badge.textContent.trim()) || 1;
                if (current > 1) {
                    badge.textContent = ` ${current - 1}`;
                } else {
                    badge.previousElementSibling?.remove(); // remove ícone
                    badge.remove(); // remove texto
                }
            }
        }


        // BOTÃO ARQUIVAR OU EXCLUIR
        document.getElementById('btn-archive-options').addEventListener('click', function(e) {
            // Remove popovers anteriores
            document.querySelectorAll('.popover').forEach(p => p.remove());

            const trigger = e.currentTarget;

            const popover = new bootstrap.Popover(trigger, {
                html: true,
                sanitize: false,
                placement: 'bottom',
                trigger: 'manual',
                title: 'O que deseja fazer?',
                content: `
      <div class="text-left">
      <p class="mb-2">Deseja apenas arquivar este card para consulta futura ou excluí-lo permanentemente?</p>
      <button class="btn btn-sm btn-warning me-2 mt-1" id="btn-confirm-archive">Arquivar</button>
      <button class="btn btn-sm btn-danger mt-1" id="btn-confirm-delete">Excluir</button>
      </div>
    `
            });

            popover.show();

            // Delay para garantir que o popover está no DOM
            setTimeout(() => {
                const cardId = document.getElementById('modal-card-id').value;
                const cardEl = document.querySelector(`.kanban-item[data-eid='${cardId}']`);
                const modalEl = bootstrap.Modal.getInstance(document.getElementById('cardModal'));

                document.getElementById('btn-confirm-archive')?.addEventListener('click', () => {
                    fetch(`/cards/${cardId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                status: 1
                            }) // arquivado
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                if (cardEl) cardEl.remove();
                                if (modalEl) modalEl.hide();
                                document.querySelectorAll('.popover').forEach(p => p.remove());
                            }
                        });
                });

                document.getElementById('btn-confirm-delete')?.addEventListener('click', () => {
                    fetch(`/cards/${cardId}`, {
                            method: 'PUT',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                status: 5
                            }) // excluído
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                if (cardEl) cardEl.remove();
                                if (modalEl) modalEl.hide();
                                document.querySelectorAll('.popover').forEach(p => p.remove());
                            }
                        });
                });
            }, 150);
        });

        // Fechar popovers ao clicar fora
        document.addEventListener('click', function(e) {
            const isInsidePopover = e.target.closest('.popover');
            const isTrigger = e.target.closest(
                '[data-bs-toggle="popover"], .btn-confirm-delete, #btn-archive-options');

            if (!isInsidePopover && !isTrigger) {
                document.querySelectorAll('.popover').forEach(p => p.remove());
            }
        });
        //FIM BOTÃO ARQUIVAR OU EXCLUIR


        //CÓDIGO PARA ORDENAR COLUNA
        document.getElementById('form-create-column')?.addEventListener('submit', function(e) {
            e.preventDefault(); // impede o refresh

            const form = e.target;
            const formData = new FormData(form);

            fetch('/columns', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const column = data.column;
                        const columnHTML = `
    <div class="kanban-board bg-light" data-id="column-${column.id}">
    <header class="kanban-title d-flex justify-content-between align-items-center p-2">
      <span>${column.name}</span>
      <div class="dropdown">
      <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item btn-change-column-color" href="#" data-id="${column.id}">Cor</a></li>
      </ul>
      </div>
    </header>
    <main class="kanban-drag" data-column-id="${column.id}"></main>
    </div>
    `;


                        document.querySelector('#kanban-wrapper').insertAdjacentHTML('beforeend', columnHTML);
                        form.reset();
                    }
                });
        });
        //FIM CÓDIGO ORDENAR COLUNA


        // ─── CODIGO COR COLUNA ────────────────────────────────────────────

        // 2) handler de clique para trocar cor via popover
        document.addEventListener('click', function(e) {
            // 2.1) Abre o popover de seleção de cor
            const trigger = e.target.closest('.btn-change-column-color');
            if (trigger) {
                e.preventDefault();
                document.querySelectorAll('.popover').forEach(p => p.remove());
                const columnId = trigger.dataset.id;
                new bootstrap.Popover(trigger, {
                    html: true,
                    sanitize: false,
                    placement: 'bottom',
                    trigger: 'manual',
                    title: 'Escolher cor',
                    content: `
      <div class="d-flex flex-wrap gap-2 p-1" style="width: 180px;">
      ${['#4A90E2', '#50E3C2', '#F5A623', '#D0021B', '#9013FE', '#FF6F61', '#7B7F9E', '#B8E986', '#F8E71C', '#FF9AA2', '#f8d7da', '#d1ecf1', '#d4edda', '#fff3cd', '#d6d8d9', '#f0f0f0']
        .map(c => `
                          <button class="btn p-3 border rounded bg-color"
                          style="background-color: ${c};"
                          data-color="${c}"
                          data-column-id="${columnId}">
                          </button>`)
        .join('')}
      </div>
      `
                }).show();
                return;
            }

            // 2.2) Ao clicar numa bolinha, aplica cor nova no header e no título
            const colorBtn = e.target.closest('.bg-color');
            if (colorBtn) {
                e.preventDefault();
                const color = colorBtn.dataset.color;
                const columnId = colorBtn.dataset.columnId;
                const boardEl = document.querySelector(`[data-id="column-${columnId}"]`);
                // Atualiza a variável CSS diretamente no .kanban-board

                if (boardEl) {
                    boardEl.style.setProperty('--col-color', color); // ✅ ATUALIZA cor no DOM
                }

                // persiste no servidor
                fetch(`/columns/${columnId}/update-color`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            color
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) console.error('Erro ao salvar cor da coluna');
                    })
                    .catch(err => console.error(err));

                // fecha o popover
                document.querySelectorAll('.popover').forEach(p => p.remove());
            }
        });
        // FIM CODIGO COR COLUNA



        //MUDAR NOME COLUNA
        document.addEventListener('click', function(e) {
            const span = e.target.closest('.column-name');
            if (!span) return;

            const columnId = span.dataset.id;
            const currentText = span.textContent.trim();

            // Cria input no lugar do span
            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control form-control-sm';
            input.value = currentText;
            input.style.minWidth = '120px';

            // Substitui o span pelo input
            span.replaceWith(input);
            input.focus();

            // Ao sair do foco ou pressionar Enter
            const save = () => {
                const newName = input.value.trim();
                if (!newName || newName === currentText) {
                    input.replaceWith(span); // volta ao normal
                    return;
                }

                fetch(`/columns/${columnId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            name: newName
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            span.textContent = newName;
                        }
                        input.replaceWith(span); // volta ao normal
                    })
                    .catch(() => {
                        input.replaceWith(span); // erro, volta
                    });
            };

            input.addEventListener('blur', save);
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') input.blur();
                if (e.key === 'Escape') input.replaceWith(span); // cancela
            });
        });
        //FIM MUDAR NOME COLUNA


        //MENU COLUNA ORDENAR CARDS
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('.btn-sort-column-cards');
            if (!trigger) return;

            e.preventDefault();
            document.querySelectorAll('.popover').forEach(p => p.remove());

            const columnId = trigger.dataset.id;

            const popover = new bootstrap.Popover(trigger, {
                html: true,
                sanitize: false,
                placement: 'bottom',
                trigger: 'manual',
                title: 'Ordenar cartões',
                content: `
      <div class="list-group">
      <button class="list-group-item list-group-item-action" data-sort="created_desc" data-column-id="${columnId}">Data recente primeiro</button>
      <button class="list-group-item list-group-item-action" data-sort="created_asc" data-column-id="${columnId}">Data antiga primeiro</button>
      <button class="list-group-item list-group-item-action" data-sort="title" data-column-id="${columnId}">Ordem alfabética</button>
      </div>
    `
            });


            popover.show();
        });


        //Handler do clique nas opções de ordenação
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-sort]');
            if (!btn) return;

            const sort = btn.dataset.sort;
            const columnId = btn.dataset.columnId;
            const columnEl = document.querySelector(`[data-id="column-${columnId}"]`);
            const cardsContainer = columnEl.querySelector('.kanban-drag');

            let cards = Array.from(cardsContainer.children);

            cards.sort((a, b) => {
                if (sort === 'title') {
                    return a.innerText.localeCompare(b.innerText);
                }

                const dateA = parseInt(a.querySelector('.kanban-card')?.getAttribute('data-created-at')) ||
                    0;
                const dateB = parseInt(b.querySelector('.kanban-card')?.getAttribute('data-created-at')) ||
                    0;

                if (sort === 'created_asc') {
                    console.log('Ordem asc:', dateA, dateB);
                    return dateA - dateB;
                }
                if (sort === 'created_desc') {
                    console.log('Ordem desc:', dateA, dateB);
                    return dateB - dateA;
                }

                return 0;
            });


            // Reaplica visualmente
            cards.forEach(c => cardsContainer.appendChild(c));

            // Atualiza no banco
            const cardIds = cards.map(c => c.getAttribute('data-eid'));
            fetch('{{ route('cards.updateOrder') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    cards: cardIds,
                    column_id: columnId
                })
            });

            // Fecha o popover
            document.querySelectorAll('.popover').forEach(p => p.remove());
        });
        //FIM Handler do clique nas opções de ordenação
        //FIM MENU COLUNA ORDENAR CARDS



        //mensagem de erros
        function showError(message) {
            const inputGroup = document.getElementById('attachment-input')?.closest('.mb-3');
            if (!inputGroup) return;

            let error = inputGroup.querySelector('.text-danger');
            if (!error) {
                error = document.createElement('div');
                error.className = 'text-danger mt-1';
                inputGroup.appendChild(error);
            }
            error.textContent = message;
        }
    </script>


@endsection
