<?php


return [
    'tasks' => [
        'form' => [
            'title' => ['label'=>'Título','placeholder'=>'Ex: Implementar autenticação de usuários'],
            'task' => ['label'=>'Tipo da Task'],
            'description' => [
                'label'=>'✨ Descrição da Funcionalidade',
                'helpText'=>'Explique o que você deseja que o sistema faça, de forma mais detalhada possivel',
                'placeholder' => '💡 Exemplo:
“O gestor financeiro precisa gerar relatórios de pedidos mensais em PDF.
Ele acessa o menu Relatórios, seleciona o mês desejado e o sistema gera um PDF com cliente, número de pedidos e valor total”.'
            ],
            'accept_criteria' => [
                'label'=>'✅ Critérios de Aceite',
                'helpText'=>'Liste o que precisa estar funcionando para você considerar a entrega correta.',
                'placeholder' => '💡 Exemplo:
“O gestor financeiro precisa gerar relatórios de pedidos mensais em PDF.
Ele acessa o menu Relatórios, seleciona o mês desejado e o sistema gera um PDF com cliente, número de pedidos e valor total”.'
            ],
            'scene_test' => [
                'label'=>'🧪 Cenários de Teste',
                'helpText'=>'Descreva situações que vamos usar para validar a funcionalidade.',
                'placeholder' => '💡 Exemplo:
Selecionar agosto deve gerar PDF com todos os pedidos de agosto.
Se não houver pedidos no período, deve gerar relatório vazio com mensagem de aviso.'
            ],
            'ovservations' => [
                'label'=>'🗒️ Observações',
                'helpText'=>'Inclua restrições, exceções ou detalhes que possam impactar o funcionamento.',
                'placeholder' => '💡 Exemplo:
Relatórios só podem ser baixados por usuários logados.
Não precisa de exportação em Excel.'
            ],
            'project_id' => [
                'label'=>'Projeto',
                'helpText'=>'Projetos onde você é proprietário ou colaborador'
            ],
            'sprint_id' => [
                'label'=>'Sprint',
                'helpText'=>'⚠️ Selecione um projeto primeiro'
            ],
            'attachments' => [
                'label'=>'Arquivos',
                'helpText' => 'Formatos aceitos: PNG, JPG, PDF, DOC, DOCX, XLS, XLSX (máx. 10MB por arquivo)'
            ]
        ]
    ],
];
