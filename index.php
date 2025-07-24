<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            overflow-x: hidden;
        }

        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .content {
            padding: 20px;
        }

        .d-none {
            display: none;
        }

        #painel-admin {
            padding-top: 5px;
            color: gray;
        }

        #painel-admin h3 {
            font-size: 26px !important;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class=" row">
            <nav class="col-md-2 sidebar">
                <div id="painel-admin">
                    <h3>Painel Admin</h3>
                </div>
                <a href="#" onclick="showPage('dashboard')">Dashboard</a>
                <a href="#" onclick="showPage('correlacoes')">Correlações</a>
                <a href="#" onclick="showPage('logs')">Ver Logs</a>
                <a href="#" onclick="showPage('ferramentas')">Ferramentas</a>
                <a href="#" onclick="logout()">Logout</a>

            </nav>
            <main class="col-md-10 content">
                <!-- Página Dashboard -->
                <div id="dashboard" class="page d-none">
                    <h1>Dashboard</h1>
                    <p>Bem-vindo ao painel administrativo!</p>
                </div>

                <!-- Página Correlações -->
                <div id="correlacoes" class="page d-none">
                    <h3>Correlações</h3>
                    <button class="btn btn-primary my-2" onclick="openForm('formCorrelacao')">Nova Correlação</button>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Campo Origem</th>
                                <th>Condição</th>
                                <th>Campo Destino</th>
                                <th>Valor Definido</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="listaCorrelacoes"></tbody>
                    </table>
                    <div id="formCorrelacao" class="d-none">
                        <h5>Nova Correlação</h5>
                        <form onsubmit="event.preventDefault(); salvarCorrelacao()">
                            <div id="step1">
                                <!-- Step 1 -->
                                <label>Ferramenta (Origem)</label>
                                <select class="form-control mb-2" onchange="carregarCamposA(this.value)" id="ferramentaA">
                                    <option value="">Selecione</option>
                                </select>
                            </div>
                            <div id="step2" class="d-none">
                                <label>Campo (Origem)</label>
                                <select class="form-control mb-2" id="campoA"></select>
                                <label>Condição</label>
                                <select class="form-control mb-2" id="condicao" onchange="toggleInputCondicional()">
                                    <option value="existe">Se existir valor</option>
                                    <option value="igual">Se igual a...</option>
                                    <option value="diferente">Se diferente de...</option>
                                </select>
                                <input type="text" class="form-control mb-2" placeholder="Valor para condicional" id="valorCondicional">
                            </div>
                            <div id="step3" class="d-none">
                                <!-- Step 3 -->
                                <label>Ferramenta (Destino)</label>
                                <select class="form-control mb-2" onchange="carregarCamposB(this.value)" id="ferramentaB">
                                    <option value="">Selecione</option>
                                </select>
                                <label>Campo (Destino)</label>
                                <select class="form-control mb-2" id="campoB"></select>
                                <label>Valor Definido</label>
                                <input type="text" class="form-control mb-2" placeholder="Valor que será atribuido ao campo destino" id="valorDefinido">
                                <button type="submit" class="btn btn-success mt-2">Salvar Correlação</button>
                            </div>
                        </form>
                        <button class="btn btn-secondary mt-2" onclick="avancarEtapa()">Próxima Etapa</button>
                    </div>
                </div>

                <!-- Página Logs -->
                <div id="logs" class="page d-none">
                    <h4>Logs de Correlações</h4>

                    <!-- Barra de Filtros -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label>Status</label>
                            <select id="filtroStatus" class="form-control" onchange="carregarLogs()">
                                <option value="">Todos</option>
                                <option value="Sucesso">Sucesso</option>
                                <option value="Erro">Erro</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Correlação</label>
                            <input type="text" id="filtroCorrelacao" class="form-control" onkeyup="carregarLogs()" placeholder="Buscar por correlação">
                        </div>
                    </div>

                    <!-- Tabela de Logs -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Correlação</th>
                                <th>Status</th>
                                <th>JSON de Entrada</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="tabelaLogs">
                            <!-- Populado via JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Página Ferramentas -->
                <div id="ferramentas" class="page d-none">
                    <h3>Ferramentas</h3>
                    <button class="btn btn-primary my-2" onclick="openForm('formCredencial')">Nova Ferramenta</button>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Status</th>
                                <th>ApiKey</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody id="listaFerramentas">
                            <!-- Populado dinamicamente -->
                        </tbody>
                    </table>

                    <div id="formCredencial" class="d-none">
                        <h5>Nova Credencial</h5>
                        <form onsubmit="event.preventDefault(); salvarCredencial()">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="Nome" id="credNome" required>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="credStatus" required>
                                        <option value="">Status</option>
                                        <option value="Ativo">Ativo</option>
                                        <option value="Inativo">Inativo</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" placeholder="ApiKey" id="credApiKey" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success mt-2">Salvar</button>
                        </form>
                    </div>
                </div>

            </main>
        </div>
    </div>
    <script>
        let etapaAtual = 1;
        let idCorrelacao = 1;
        let listaFerramentas = [];
        let credencialId = 1;
        let logsCorrelacoes = [];

        const campos = {
            "Contact2Sale": ["Motivo Cancelamento", "Motivo Arquivamento", "Etiquetas"],
            "RD Station": ["cf_motivo_cancelamento", "cf_motivo_arquivamento", "cf_etiquetas"],
            "Hubspot": ["hs_motivo_cancelamento", "hs_motivo_arquivamento", "hs_etiquetas"]
        };

        const logs = [{
                nome: "João Silva",
                correlacao: "Contact2Sale x Hubspot",
                status: "Sucesso",
                jsonEntrada: '{"email":"joao@gmail.com","telefone":"119999999"}'
            },
            {
                nome: "Maria Oliveira",
                correlacao: "Contact2Sale x Hubspot",
                status: "Sucesso",
                jsonEntrada: '{"email":"maria@outlook.com"}'
            },
            {
                nome: "Fernando",
                correlacao: "RD Station x Contact2Sale",
                status: "Erro",
                jsonEntrada: '{"email":"fernando@outlook.com"}'
            },
            {
                nome: "Jose Fonseca",
                correlacao: "RD Station x Contact2Sale",
                status: "Sucesso",
                jsonEntrada: '{"email":"jose@outlook.com"}'
            },
            {
                nome: "Lucas Gomes",
                correlacao: "Hubspot x Contatc2Sale",
                status: "Erro",
                jsonEntrada: '{"email":"lucas@outlook.com"}'
            }
        ];


        function carregarLogs() {
            const statusFiltro = document.getElementById("filtroStatus").value.toLowerCase();
            const correlacaoFiltro = document.getElementById("filtroCorrelacao").value.toLowerCase();

            const tabela = document.getElementById("tabelaLogs");
            tabela.innerHTML = "";

            const logsFiltrados = logs.filter(log => {
                const statusOK = !statusFiltro || log.status.toLowerCase() === statusFiltro;
                const correlacaoOK = !correlacaoFiltro || log.correlacao.toLowerCase().includes(correlacaoFiltro);
                return statusOK && correlacaoOK;
            });

            logsFiltrados.forEach((log, index) => {
                const row = document.createElement("tr");
                row.innerHTML = `
                <td>${log.nome}</td>
                <td>${log.correlacao}</td>
                <td><span class="badge ${log.status === 'Sucesso' ? 'bg-success' : 'bg-danger'}">${log.status}</span></td>
                <td><pre class="mb-0" style="white-space: pre-wrap;">${log.jsonEntrada}</pre></td>
                <td><button class="btn btn-sm btn-primary" onclick="reenviarLog(${index})">Reenviar</button></td>
            `;
                tabela.appendChild(row);
            });
        }

        function reenviarLog(index) {
            const log = logs[index];
            alert(`Reenviando log de ${log.nome} (${log.correlacao})`);
        }

        function salvarCredencial() {
            const nome = document.getElementById("credNome").value;
            const status = document.getElementById("credStatus").value;
            const apikey = document.getElementById("credApiKey").value;

            const credencial = {
                id: credencialId,
                nome,
                status,
                apikey
            };

            listaFerramentas.push(credencial);

            const novaLinha = `
        <tr>
            <td>${credencialId}</td>
            <td>${nome}</td>
            <td>${status}</td>
            <td>${apikey}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editItem('credencial', ${credencialId})">Editar</button>
                <button class="btn btn-sm btn-danger" onclick="deleteItem('credencial', ${credencialId})">Deletar</button>
            </td>
        </tr>
    `;

            document.getElementById("listaFerramentas").insertAdjacentHTML("beforeend", novaLinha);
            credencialId++;

            alert("Credencial salva com sucesso!");

            document.querySelector('#formCredencial form').reset();
            document.getElementById("formCredencial").classList.add("d-none");

            atualizarSelectFerramentas();
        }

        function atualizarSelectFerramentas() {
            const selectA = document.getElementById("ferramentaA");
            const selectB = document.getElementById("ferramentaB");

            // Limpa tudo e adiciona a opção padrão
            selectA.innerHTML = `<option value="">Selecione</option>`;
            selectB.innerHTML = `<option value="">Selecione</option>`;

            listaFerramentas.forEach(cred => {
                if (cred.status === "Ativo") {
                    const optA = document.createElement("option");
                    const optB = document.createElement("option");
                    optA.value = cred.nome;
                    optA.textContent = cred.nome;
                    optB.value = cred.nome;
                    optB.textContent = cred.nome;
                    selectA.appendChild(optA);
                    selectB.appendChild(optB);
                }
            });
        }


        function avancarEtapa() {
            if (etapaAtual === 1 && document.getElementById('ferramentaA').value !== "") {
                document.getElementById('step2').classList.remove('d-none');
                etapaAtual++;
            } else if (etapaAtual === 2 && document.getElementById('campoA').value !== "") {
                document.getElementById('step3').classList.remove('d-none');
                etapaAtual++;
            }
        }

        function toggleInputCondicional() {
            const cond = document.getElementById("condicao").value;
            document.getElementById("valorCondicional").classList.toggle('d-none', cond === 'existe');
        }

        function carregarCamposA(ferramenta) {
            const campoSelect = document.getElementById("campoA");
            campoSelect.innerHTML = campos[ferramenta]?.map(c => `<option value="${c}">${c}</option>`).join("") || "";
        }

        function carregarCamposB(ferramenta) {
            const campoSelect = document.getElementById("campoB");
            campoSelect.innerHTML = campos[ferramenta]?.map(c => `<option value="${c}">${c}</option>`).join("") || "";
        }


        function salvarCorrelacao() {
            const ferramentaA = document.getElementById('ferramentaA').value;
            const campoA = document.getElementById('campoA').value;
            const condicao = document.getElementById('condicao').value;
            const valorCondicional = document.getElementById('valorCondicional').value;
            const ferramentaB = document.getElementById('ferramentaB').value;
            const campoB = document.getElementById('campoB').value;
            const valorDefinido = document.getElementById('valorDefinido').value;

            const nome = ferramentaA + ' → ' + ferramentaB;
            const condDesc = condicao === 'existe' ? 'Existe valor' : `${condicao === 'igual' ? 'Igual a' : 'Diferente de'} "${valorCondicional}"`;

            const novaLinha = `
                <tr>
                    <td>${idCorrelacao++}</td>
                    <td>${nome}</td>
                    <td>${campoA}</td>
                    <td>${condDesc}</td>
                    <td>${campoB}</td>
                    <td>${valorDefinido}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editItem('correlacao', ${idCorrelacao})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteItem('correlacao', ${idCorrelacao})">Deletar</button>
                    </td>
                </tr>`;

            document.getElementById('listaCorrelacoes').insertAdjacentHTML('beforeend', novaLinha);
            alert(`Correlação "${nome}" criada com sucesso!`);
            document.querySelector('#formCorrelacao form').reset();
            document.getElementById('step2').classList.add('d-none');
            document.getElementById('step3').classList.add('d-none');
            etapaAtual = 1;
        }

        // function showPage(pageId) {
        //     document.querySelectorAll('.page').forEach(p => p.classList.add('d-none'));
        //     document.getElementById(pageId).classList.remove('d-none');
        // }

        function showPage(pageId) {
            document.querySelectorAll(".page").forEach(el => el.classList.add("d-none"));
            document.getElementById(pageId).classList.remove("d-none");

            if (pageId === 'logs') {
                carregarLogs();
            }

        }


        function openForm(formId) {
            document.getElementById(formId).classList.toggle('d-none');
        }

        function editItem(tipo, id) {
            alert(`Editar ${tipo} com ID: ${id}`);
        }

        function deleteItem(tipo, id) {
            if (confirm(`Deseja deletar ${tipo} com ID: ${id}?`)) {
                alert(`${tipo} ${id} deletado.`);
            }
        }

        function logout() {
            alert('Você foi desconectado.');
        }
        document.addEventListener("DOMContentLoaded", function() {
            showPage('dashboard');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>