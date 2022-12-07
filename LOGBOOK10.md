# Cross-Site Scripting (XSS)

### Environment Setup

Seed lab week 10 set up a docker environment compressing a web server and a MySQL database that supports the information stored in that platform.

This web application is an open-source social network named Elgg where some countermeasures were relaxed to be able to replicate the Samy Worm of 2005 in MySpace.

It is suggested to install a tool that allows an easy inspection of the HTTP requests made in the background while browsing the Firefox Browser. **HTTP Header Live**


### Task 1: Posting a Malicious Message to Display an Alert Window

The handout tells us to inject this inline javascript code in the brief description field under the profile edition. If the input is not properly sanitized

```javascript
<script>alert(’XSS’);</script>
```

The result was the javascript alert appearing in the browser frame. This means we can use a brief description to perform XSS stored attacks.

### Task 2: Posting a Malicious Message to Display Cookies

In task 1 we simply injected a line of code that does nothing harmful, simply display the 'XSS' in a Javascript alert. Nevertheless, we are facing a vulnerability that can be exploited for bad.

In task 2 we want to increase the severity of the attacks using a brief description of input vulnerability. We will output in an alert the cookie variable content in the javascript alert

By changing the input of task 1 to the following line:
```javascript
<script>alert(document.cookie);</script>
```

The output was the following:
<div style="text-align:center">
    
![](https://i.imgur.com/P7fS4jt.png)

</div>

### Task 3: Stealing Cookies from the Victim’s Machine

One common attack described extensively in the theoretical class, was the use of XSS to trigger a connection to the browser on the client-side that will be interpreted as intended by the user, and therefore will execute with the client privileges, namely it will send the cookies associated with the destination website.

Then by performing usually eavesdropping on the network, in this case by targeting the request towards a malicious Socket connection with the cookie as payload. The attacker can collect sensitive information about the user.

The simplicity of this attack required platforms to enforce measures against the privileges somebody with a session cookie could access, since easily, as we saw, it can be stolen.


### Task 4: Becoming the Victim’s Friend


The goal of this task is the exploitation of XSS vulnerability to imitate part of behaviour that was presented in the already described Sandy Worm. When somebody visits the profile of the user Sandy of Elgg, that person becomes automatically a friend of Sandy.

#### Understand How Friend Requests are Made

To understand how we can handle this task, Seedlab tells us to first verify how friend requests are made. What is the URL and payload sent to a user during a friend request? **To achieve this goal we made use of HTTP Header Live**.

After opening HTTP Header Live, we sent a friend request from Alice towards Charlie. The URL that constituted this request was:

<div style="text-align:center">
    
![](https://i.imgur.com/HOIePD2.png)

</div>

A HTTP GET request for 

* Path : **/action/friends/add**
* Query String:
    * friend=friend_id
    * elgg_ts(timestamp)=timestamp
    * elgg_token=cookie


The timestamp and the token which is the session cookie provided by the Elgg server upon login are replicated twice to perform a syntactically well-formed friend request in this application.

#### Exploit

To trigger a GET request dynamically, we will inject code that could perform an Ajax request to correct the route. 

By performing a previous friend request to Samy we determined that **Samy id was 59**. With this information and using the example code provided already by Seedlabs, we reached out to the following code.

We added an extra alert() so that it was easier to detect the code was taking effect.

<div style="text-align:center">
    
![](https://i.imgur.com/ka29hoz.png)

</div>

With the payload crafted, we then introduced it as suggested in the handout in the about me field on Samy profile. It was necessary to select the option HTML mode before submitting the payload to it to take effect(See question 2 below).

After that we open Alice account, we went to Samy profile and the effect was the following.
<div style="text-align:center">
    
![](https://i.imgur.com/9RyDexF.png)

</div>


Question Answering:

* Question 1: Line 1 and 2 are required to perform a well-formed friend request. Before crafting the payload we made a manual friend request to understand what the syntax was, and it demanded inserting the pair, timestamp-token, twice in the request towards the server. Otherwise, the request was malformed and the server would ignore it. Lines 1 and 2 read those required values that are private and unique between users(and for that reason cannot be hardcoded), require a dynamic extraction from the URL code of that information.


* Question 2: No, we didn't notice that (we didn't read it completely) and the attack was not taking place the first time we tried it. We then recalled to the Seedlab and this time we notice this question and fixed it. After submitting in HTML form we completed the exploit. We bet that internally authors have two distinct pipelines, the text mode that dumps the content in HTML raw text, and the HTML mode that interprets it as executable HTML, a clear wrong decision from developers.
