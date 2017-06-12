# TwimiCenter

## User Login
URL : `api.php?mod=login&username=[Your Username]&password=[Your Password]`

Response:

```
{
    "status":"ok",
    "id":"[Your User ID]",
    "email":"[Your Email]",
    "nickname":"[Your Nickname]"
}
```
    
------------

## User Register
URL : `api.php?mod=register&username=[Your Username]&password=[Your Password]&email=[Your Email]&nickname=[Your Nickname(Optional)]`

Response:

```
{
    "status":"ok",
    "id":"[Your User ID]",
    "email":"[Your Email]",
    "nickname":"[Your Nickname]"
}
```

------------

## Post New Post
URL : `api.php?mod=post&action=post&token=[Your AccessToken]&title=[Post Title]&message=[Post Content]`

Response:

```
{
    "status":"ok",
    "tid":"[Your Post ID]"
}
```

------------

## Post View Post List
URL : `api.php?mod=post&action=view&page=[Post List Page(Optional)]`

Response:

```
{
    "status":"ok",
    "page":"[Current Page]",
    "posts":[
        {
            "tid":"[Post ID]",
            "username":"[Post Creater]",
            "nickname":"[Post Creater's Nickname]",
            "title":"[Post Title]",
            "message":"[Post Content]",
            "timeline":"[time stamp]"
        }
    ]
}
```

------------