*** UTILIZADORES *** 
admin:admin (admin faz tudo)
Joao:4321    (é funcionario) nao pode ir historico nem grafico
Tiago:1234   (hospede )so vai à sua dashboard
É possível adicionar users através de register.php

***ESTRUTURA***

Página Inicial: index.php
Menu/Navbar: menu.php (está incluido em todas as páginas)
Página Login: login.php
Página registo: register.php
Dashboard page: dashboard.php (está incluido um painel.php ou painelHospede.php, que tem os cards e tabela)
        nota: para aceder ao histórico individual - passar o mouse por cima do card
Página gráfico: graficoLotacao.php (mostra o gráfico da lotacao)
Historico individual: historico.php (funciona com um GET ex: historico.php?nome=temperatura)
Historico com todos os sensores/atuadores: historicoAll.php (mostra últimas 3 linhas de cada um)
Historico das imagens: historicoImages.php (mostra o historico de todas as imagens)
Logout.php: terminar a sessão do utilizador
Footer: footer.php (está incluido em todas as páginas)

nota: Montserrat é a pasta com a fonte utilizada

