# blue-wp-categories
Sync WordPress Categories from an external source into the WordPress.

## WordPress Categories

### Installation

- [Download](https://github.com/desaiuditd/blue-wp-categories/archive/master.zip) the plugin from the Github.
- Go to `Plugins > Add New > Upload Plugin`.
- Upload the zip.
- Alternatively, you can unzip the plugin zip file and upload it to the plugins folder of your WordPress installation (`wp-content/plugins/` directory of your WordPress installation).
- Activate it through the `Plugins` section.

### Setup

- After installing / activating the plugin, go to `Settings > General`.
- In the `Blue WordPress Category Sync` section, fill in the valid url of external API endpoint for Categories.
- This step is manadatory to begin the category sync.
- The Cron job to sync categories every 30 minutes, will be activated, when the plugin is activated. The cron will be disabled when the plugin is deactivated.

### Work Left in Progress

There are few edge cases to cover in this code. E.g.:

- If you disable manage category from the options, it disables tags as well, along with the categories. We should ideally seperate those two.
- Better on page documentation to let the user know what plugin does, and how it is useful.

### Test Feedback

The test was very well planned and challenging. It requires all major concepts and knowledge of WordPress for the developer to know e.g.,:

- Plugin Development
- Hooks / Action
- Terms / Taxonomy and their relationships
- Term Meta
- Roles and Capabilities
- Cron

It was very fun completing this test.

### Duration to complete the Test

Approximately 4-5 hours that includes following:

- Complete development time to implement all the requirement.
- Testing for the code
- WordPress Website Deployment on AWS Server to setup a live demo for the WordPress plugin.
- Local Development Setup on Vagrant

### Summary

The purpose of this exercise is for us to get a sense of how you would approach designing and implementing a simple WordPress integration before we get you in for an interview. We’re avoiding tricky algorithmic tests in favor of something that shows how you approach problems and organise a codebase.

There is no time limit for this test, but we expect most applicants to complete the requirements in roughly 3-4 hours.

Feel free to use any PHP framework you like, but note that submissions utilising WordPress will be looked on favourably.

If you make any assumptions about requirements, or use any online resources to solve a problem, please make note of these in your code comments.

Your solution will be evaluated internally by your potential co-workers. You should expect a response from us within two business days.

### User story

As a WordPress system

I want to get all categories from an external API

So that a single system handles taxonomy and their relationships

### Acceptance criteria

1. WordPress polls a fake API (see dev notes below) every 30 minutes to check for changes to categories
2. Any change to categories in the API should be reflected in WordPress
3. The hierarchy of categories should be maintained (i.e. parent/child relationship)
4. Add `Update categories now` button to Settings/General in the WordPress admin interface that will update categories on demand
5. Code for the above to be available via GitHub or BitBucket repo
6. Repo should have README.md that contains the following:
   - Any project quirks or setup notes
   - Any work left in progress
   - A short paragraph outlining what you thought of the test
   - How long the test took to complete
   
### Bonus

The following tasks are not required, but nice to have:

1. Test WordPress environment to be hosted and publicly accessible (with username/password)

2. Disable ability to add new categories from within WordPress


### Dev notes

You can create a fake REST API with [JSON Server](https://github.com/typicode/json-server).

**Example db.json:**

```json
{
  "categories": [
    {
      "id": 1000,
      "name": "State",
      "parent_id": null
    },
    {
      "id": 1001,
      "name": "NSW",
      "parent_id": 1000
    },
    {
      "id": 1002,
      "name": "VIC",
      "parent_id": 1000
    },
    {
      "id": 1003,
      "name": "QLD",
      "parent_id": 1000
    },
    {
      "id": 1004,
      "name": "WA",
      "parent_id": 1000
    },
    {
      "id": 1005,
      "name": "ACT",
      "parent_id": 1000
    },
    {
      "id": 1006,
      "name": "National",
      "parent_id": null
    },
    {
      "id": 1007,
      "name": "World",
      "parent_id": null
    }
  ]
}
```

### Deliverables

All acceptance criteria should be met (to best of ability and time available). A link to your repo should be sent to: `blueco DOT tech DOT test AT gmail DOT com`

