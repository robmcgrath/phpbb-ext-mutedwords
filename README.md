# Muted Words — phpBB extension

phpBB extension adding a **Muted Words** tab to the UCP *Friends & Foes* section. Each user keeps a personal list of words or phrases, and topics whose **titles** contain any of them are hidden from that user while browsing forums and viewing search results.

## Requirements

- phpBB 3.3.x
- PHP 7.2 or newer

## Installation

1. Download the latest release (or clone this repository).
2. Copy the contents of this repository to `ext/robmcgrath/mutedwords/` inside your phpBB installation, so that `ext/robmcgrath/mutedwords/composer.json` exists.
3. In the ACP, go to **Customise → Manage extensions** and enable **Muted Words**.

## Usage

1. Open the **User Control Panel** and go to **Friends & Foes → Muted Words**.
2. Enter a word or phrase (max. 255 characters) and click **Add**.
3. Your muted words are listed below the form; click **Delete** next to a word to remove it.

Matching rules:

- Only **topic titles** are checked — post contents are never scanned.
- Matching is a case-insensitive **substring** match (UTF-8 aware), so muting `cat` also hides `Category news`.
- Matching topics are hidden in forum topic lists (`viewforum.php`) and in search results (`search.php`, including "new posts", "unanswered topics", "active topics", etc.). In post-view search results, posts belonging to muted topics are hidden.
- Guests and bots are never filtered; moderator (MCP) views are not filtered.

## Known limitations

- Topics are removed from the already-fetched page of results, so **pagination and topic/result counts may still include hidden topics**, and a page can show fewer topics than the configured per-page amount (or even none).
- Hidden topics are not considered when phpBB decides whether to mark a forum as read on `viewforum.php`, so unread muted topics do not keep a forum marked as unread.
- The "last post" information on the board index / forum list is not filtered.

## Uninstall

In the ACP, disable the extension, then **Delete data** to drop the `muted_words` table and remove the UCP module. Finally delete `ext/robmcgrath/mutedwords/`.

## License

[GNU General Public License v2](LICENSE)
