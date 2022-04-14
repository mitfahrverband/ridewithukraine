# ridewithukraine

## Config

`src/config.ini`

### Database

Replace the following words with actual credentials:

- DATABASE
- USER
- PASSWORD

## Local execution

`php -S 127.0.0.1:80 -t ./src/public`

## Tailwind CSS rebuild

Only needed if tailwind classes are changed/added.

```
cd npm
npm install
npm run tailwind
```

The command watches for file changes in directories specified in `npm/tailwind.config.js`
and automatically rebuilds the css.
