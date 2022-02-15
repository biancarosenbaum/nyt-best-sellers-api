# Lendflow Assessment: NYT Best Sellers List &amp; Filter

Please create a tiny Laravel JSON API around the New York Times Best Sellers History API.

## 1. Create New York Times API Credentials

You need to create your own API credentials to access the NYT API.

1. Create a New York Times developer account: https://developer.nytimes.com/accounts/create
1. Go to create a New App: https://developer.nytimes.com/my-apps/new-app
1. Enable the Books API.
1. Create your app.
1. Copy your API key locally.

## 2. Create the Laravel JSON API

Use Laravel to create a JSON API around the NYT Best Sellers endpoint. Your app should expose a single endpoint:

```
GET /api/1/nyt/best-sellers
```

This endpoint should support the following subset of the NYT API&#39;s Query Parameters:

```
author: string
isbn[]: string
title: string
offset: integer
```

All filters above are optional. ISBN is 10 or 13 digits. Multiple ISBN can be searched at once. Do take note of the format the NYT API expects multiple ISBNs. Offset must be a multiple of 20. Zero is a valid offset.

### Expectations

While implementation is up to you, it is expected that you will make use of Laravel&#39;s Form Requests, HTTP Client, and HTTP Tests.

- The endpoint should be well tested.
- Tests should pass without valid NYT API credentials or an active internet connection.
- Tests should consider edge/failure cases.

## 3. Delivery

Please post your code to Github, Gitlab, Bitbucket, or similar and send us a link.
