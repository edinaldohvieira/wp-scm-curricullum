# wp-scm-curricullum
Plugin do WordPress - Exibe uma LISTAGEM, busca e DETALHES dos CURRICULLUMs publicados por cada usuário.  
  
Este plugin permite através de shortcode (atalhos de códigos) configurar em qual página desejamos exibir a listagem, detalhes e buscas referente ao cadastro de currículos cadastrados por cada usuários.  
  
Consideramos 3 níveis de utilização, a saber:  
1 - O próprio usuário é quem se cadastra e completa as informações sobre seu próprio currículo.  
2 - O administrador também pode cadastrar os currículos dos outros usuários.  
4 - O público em geral (internauta visitante) lista, filtra e ver os detalhes de cada currículo.  
  
Utilize os shortcodes:  
  
#### [wp_scm_curricullum_list]  
Utilize este shortcode na página onde vocẽ quer que seja exibida a listagem. Exemplo: "/curriculos" ou "/consultores"  
  
#### [wp_scm_curricullum_det]  
Utilize este shortcode na página onde vocẽ quer que seja exibida os detalhes de cada curriculo. geralmente é a página exibida quando se clica em um curriculo da listagem.
  
#### [wp_scm_curricullum_busca]  
Utilize este shortcode para exibir um formulário de busca ou filtragem dos currícullus cadastrados. Geralmente esse shortcode é utilizados no widget lateral.  
  
#### [wp_scm_curricullum_user_panel]  
Utilize este shortcode para permitir a edição dos detalhes feita pelo próprio usuário a que pertence o currículo. Cada usuário só pode alterar seu próprio currículo.  

#### [wp_scm_curricullum_admin_panel]  
Utilize este short code em uma página especifica para permitr que administradores possam alterar qualquer curriculo.





## MODIFICAÇÕES E NOVOS RECURSOS PREVISTOS
- A foto do currículo é a mesma foto do usuario

## INFORMAÇÕES ADICIONAIS

### Campos xtras no cadastro de usuário
Este plugin adiciona campos extra no cadastro de usuário.  
Os campos adicionados são:  
- scm066_estrelas  
- scm066_nivel  
  

### POST-TYPE CURRICULLUM
Para os detalhes do currícullum, cada usuário posta em um post-type a parte chamado de "curricullum" as informações que junto com os campos do cadastro de usuário formam o que podemos chamar de "CURRICULLUM DO USUARIO".  
  

  

## Logs
### 0.4  
A foto do curricullum é a mesma do cadastro de usuário do WordPress.   
[![video mostrando como é feito o upload da foto para o curricullum](http://img.youtube.com/vi/u9yHww7tegw/0.jpg)](https://youtu.be/u9yHww7tegw "Upload da foto - Clique para ver o video no youtube.")
  
Um [video mostrando como é feito o upload da foto para o currícullum](http://img.youtube.com/vi/u9yHww7tegw/0.jpg). Nesse vídeo estamos usando os recursos do plugin "Ultimate Member" para fazer o upload e cortar a foto.  
  
### v0.5  
#### Corecao do nome do usuario. 

### v0.6  
#### Ajustes do campo RAMO DE ATIVIDADE. 




