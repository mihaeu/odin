# Odin - Same same, but different

**NOTE: This is under heavy construction and right now I would not recommend anyone to try this out.**
**Try Jekyll/Octopress (Ruby), Pelican (Python), or Assemble (NodeJS) instead.**
**I love all of them and they inspired this project quite a bit.**

**Also note that this is more like a wish-list than actual documentation :P**


Odin is yet another static site generator, but with an approach that is slightly different.

There are lots of static site/blog generators out there in most of the commonly used languages:

- Octopress (Ruby)
- Jekyll (Ruby)
- Pelican (Python)
- Hyde (Python)
- ...

And about two dozen written in PHP. I tried most of those who went through the effort of documenting their project (which already excludes more than half of those) and even some of those who didn't. They just don't compare to the Ruby/Python alternatives. The two most famous ones are Phrozn and Sculpin. Both are well written, but Phrozn is not flexible and requires a lot of handy work to roll a blog and Sculpin is not documented at all.

Some people don't want to go through the effort of setting up another language and besides, PHP is the most commonly used language on the web. The should be a site generator who can compete with the lesser used alternatives. This is my attempt at creating one.

What I love about Octopress is that the default theme is pretty sweet. It looks decent enough, is widget ready (which are also provided with Octopress) and is search engine friendly. It's just really really convenient for those who want to get started right away.

But I'm missing a bootstrap tool that gets me started even faster without opening the config and the theme is not separated from the content, making a switch much more difficult.

## Features

### Blog in less than 1 minute
All it takes to get started is one command, the rest is easy as pie. The default theme will provide you with almost everything you need and maybe more.

### Well documented
I tried to keep using Odin as simple as possible, but made sure to comment all of the use cases I could think about. Disagree? [Let me know](http://someLINK)

### Flexible
Odin does whatever you tell him to. Blog like a maniac, compile documentation or create a basic website for a small business.

### Content Managment
Yes, Odin actually provides a CMS. This is for when you want to quickly create a website or a small business using static site generation, while still giving your customer limited control over the content. We're not talking Wordpress here, but most of the time, that's not what people are looking for. Change a promotion, raise prices, remove an old staff member form the about page etc.

### Configuration AND/OR Convention
Have it your way:

Prefer using a command line tool instead of playing around in YAML files? You can adjust most of the basic settings on the fly using the command line tool. If you rather tinker with your site using a configuration file under VCS, you can also do just that. Or if you want you could just stick to a couple of conventions and avoid having to do any configuration.

## Installation

I'm assuming you are familiar with Composer and it is installed globally. If you are not, you can read more about it [here](http://LINK).

I tried to make the install as simple as possible, so all it takes is one command:

```bash
$ composer create-project mihaeu/odin-blog [/var/www/myNewBlog]
```

## Usage

### Basic

### Developing / Extending Odin

Odin is flexible and easily bends to your will, yet there is one design decision that should always be kept in mind and that is the specificity of configuration and convention (from lowest to highest):

- convention (e.g. filename includes title and date `2014-01-01-awesome-post.md` )
- global configuration (e.g. `default_layout: index.html.twig`)
- local configuration (e.g. Frontmatter property `layout: landing-page.html.twig`)
- command line arguments (e.g. setting different blog URL via `odin --blog_url="http://localhost:1234" ... `)

Therefore the file `2014-01-01-awesome-post.md` with the Frontmatter

```yml
 ---
 title: Awesomererer post
 ---
```

 Will be compiled to carry the title `Awesomererer post`. And most of the basic blog settings can be overwritten using command line arguments (this is to make testing and deploying easier).

 So for those who don't like to meddle around config files: you don't have to. You can set up the blog using the odin tool and compose your content using a few very simple conventions.

## Todo

- generators that produce content
- bulletproof pathing
- documentation
- api generation
- phpunit

- pandoc support
- setup wizard for odin and odin-plugins
- different input formats
