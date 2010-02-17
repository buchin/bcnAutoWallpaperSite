=== Review Box ===
Contributors: paradox460
Tags: review, rating, box, post, cons, pros, score
Requires at least: 2.5
Tested up to: 2.8.4
Stable tag: 1.5

Provides a simple shortcode to generate a box for writing reviews. Supports pros, cons, and a numerical score.

== Description ==

Provides a shortcode for use by reviewers.

Generates a box, for use in a post or page, with a section for Pros, a section for Cons, and a review bar.

The review bar is generated through CSS, and so the plugin contains **NO** images.


== Installation ==

1. Upload the `review-box` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In a post, place `[review pros="list of pros" cons="list of cons" score=75]` where you would like the box to appear

You can optionally add custom titles and verdicts.
Custom titles are defined using the `title=` property, ex `title='Custom
title'`. If you do not want to display the title box at all, set `title` to
`none`

For the verdict box, add `verdict='final verdict'`. This is optional as well


== Frequently Asked Questions ==

= Do i have to fill out all 3 properties =

No, but it is reccomended. If you do not fill them out, they will have a default value.

The default values are fairly amusing.

If you do not fill out the score value, the plugin will default to 100

= What format should the score value be in =

The score should be an integer between 0 and 100. It will ultimately come out as a percentage.
**The score does not need a % sign following it. Adding one will make the sky fall on your head**

= The score gets messed up! Help =

This is probably because you added a % (percent) sign to the end of the
`score` property. All you need to input is a number
== Screenshots ==
1. Example of this plugin's output. Quite clean, isnt it?

== Changelog ==
= 1.5 =
* No longer uses percents to format width. Width is controlled
 programattically. To change, adjust variable `$width` in the plugin file.
* Added title and verdict options. If title is set to none, no title is
displayed
* Changed structure of plugin, now has a cleaner return
* Tweaked CSS, added text shadows
* Updated to work with 2.8.*
= 1.0.3 =
* Fixed Css width, now renders at proper size
* Added border to deliniate total section.
= 1.0.2 =
* Fixed stupid commit bug
= 1.0.1 =
* Fixed CSS loading bug.
* Added screenshot
= 1.0 =
* Created plugin

== Usage ==
This plugin generates a simple shortcode to use.
That shortcode is `[review pros="" cons="" score=]`

The paramiters are required for proper output.
They are as follows
* **pros:** Anything you find good about the item being reviewed. Typically a comma seperated list
* **cons:** Anything bad about the product at hand. A comma seperated list as well
* **score:** This **MUST** be a number. There can be nothing following it, nor anything before it. A good example of the way to fill it out would be `score=45` for an item that scored 45%

You can also provide the following optional paramiters, for your useage
* **verdict:** The final verdict for the item being reveiwed
* **title:** The title of the review box, defaults to review. Set it to `none`
 to hide the title. You CAN enter shortcodes and html for custom links.
