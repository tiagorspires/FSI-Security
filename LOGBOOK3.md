
# Trabalho realizado na Semana #3

## Identificação

- DNS Cache Poisoning Issue ("Kaminsky bug") (CVE-2008-1447)
- Permite aos atacantes redirecionar os utilizadores para sites maliciosos
- Não existe um sistema operativo ou aplicação especifica, é um ataque ao DNS

## Catalogação

- CVSS score: 5.0
- Descoberto por Dan Kaminsky em 2008
- Dan Kaminsky falava com um amigo que lhe perguntou como ele aceleraria o CDN (Content Distribution Network) e Dan encontrou esta vulnerabilidade
- Ataque ativo

## Exploit

- Fraqueza no proprio protocolo de DNS
- O campo de ID da consulta é apenas 16 bits, o que faz dele um alvo fácil de explorar no cenário de spoofing

## Ataques

- Redireccionar clientes de rede para servidores alternativos da sua escolha
- Kaminsky descobriu essa falha no DNS. Usando de forma recursiva para encontrar um IP na internet, 
temos de pedir a um root server. Um exemplo de website, "exemplo.com", o root neste caso também não conhece o website que procurou, mas conhece websites ".com", então envia por sua vez outro servidor que possa ajudar na sua busca. Sempre que é feita 
esta busca, vai guardando os IP's num DNS Cache, e é aqui que está o centro do problema. A mesma query
de busca pelo website "exemplo.com", é feita ao servidor que o root nos deu. Continuando a busca, esse servidor 
dá-lhe outro servidor que está mais perto de encontrar o IP do "exemplo.com", guardando novamente no DNS cache.
Recursivamente isto acontece até encontrar o IP, então guardando o "exemplo.com" no DNS Cache com o IP que o 
ultimo servidor lhe deu, para não ter de fazer sempre a mesma busca. Para o NameServer ter em conta de todas as perguntas
e respostas que são feitas, adiciona um ID de 16 bits de transação, a resposta vai junto com esse ID, guardando no cache também.
Kaminsky aproveitou esta falha, ao fazer-se passar de um servidor que envia respostas a um cliente que busca 
o website "exemplo.com", ao tentar adivinhar o ID de transação até acertar o correto. Ao fazer isso, é guardado 
no cache do cliente (vitima) o IP que Kaminsky quiser que seja. O website "exemplo.com" pode redirecionar para 
o IP que ele escolheu, um exemplo seria "google.com". Isto funciona graças ao cache que dura 7 dias até reiniciar.
