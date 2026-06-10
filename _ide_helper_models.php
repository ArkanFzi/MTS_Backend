<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models\Auth{
/**
 * @property string $id
 * @property string $name
 * @property array<array-key, mixed>|null $permissions
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role wherePermissions($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models\Auth{
/**
 * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string|null $avatar_url
 * @property string|null $bio
 * @property int $reputation_points
 * @property int $level
 * @property bool $is_banned
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gamification\Badge> $badges
 * @property-read int|null $badges_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interaction\Bookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $followers
 * @property-read int|null $followers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $following
 * @property-read int|null $following_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Gamification\PointsLog> $pointsLogs
 * @property-read int|null $points_logs_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content\Post> $posts
 * @property-read int|null $posts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\Auth\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereBio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsBanned($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereReputationPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models\Auth{
/**
 * @property string $id
 * @property string $user_id
 * @property string $role_id
 * @property \Illuminate\Support\Carbon $assigned_at
 * @property-read \App\Models\Auth\Role $role
 * @property-read \App\Models\Auth\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereAssignedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserRole whereUserId($value)
 */
	class UserRole extends \Eloquent {}
}

namespace App\Models\Content{
/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $parent_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read Category|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content\Post> $posts
 * @property-read int|null $posts_count
 * @method static \Database\Factories\Content\CategoryFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereSlug($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models\Content{
/**
 * @property string $id
 * @property string $post_id
 * @property string $user_id
 * @property string|null $parent_id
 * @property string $body
 * @property int $vote_score
 * @property bool $is_accepted
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\History\CommentEditHistory> $editHistories
 * @property-read int|null $edit_histories_count
 * @property-read Comment|null $parent
 * @property-read \App\Models\Content\Post|null $post
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Comment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\Auth\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interaction\Vote> $votes
 * @property-read int|null $votes_count
 * @method static \Database\Factories\Content\CommentFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereIsAccepted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment whereVoteScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment withoutTrashed()
 */
	class Comment extends \Eloquent {}
}

namespace App\Models\Content{
/**
 * @property string $id
 * @property string $user_id
 * @property string $category_id
 * @property string $title
 * @property string $body
 * @property string $status
 * @property int $view_count
 * @property int $vote_score
 * @property bool $is_answered
 * @property string|null $accepted_answer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Content\Comment|null $acceptedAnswer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interaction\Bookmark> $bookmarks
 * @property-read int|null $bookmarks_count
 * @property-read \App\Models\Content\Category $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content\Comment> $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\History\PostEditHistory> $editHistories
 * @property-read int|null $edit_histories_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content\Tag> $tags
 * @property-read int|null $tags_count
 * @property-read \App\Models\Auth\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Interaction\Vote> $votes
 * @property-read int|null $votes_count
 * @method static \Database\Factories\Content\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereAcceptedAnswerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereIsAnswered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereViewCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereVoteScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withoutTrashed()
 */
	class Post extends \Eloquent {}
}

namespace App\Models\Content{
/**
 * @property string $id
 * @property string $post_id
 * @property string $tag_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostTag whereTagId($value)
 */
	class PostTag extends \Eloquent {}
}

namespace App\Models\Content{
/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $color
 * @property int $usage_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Content\Post> $posts
 * @property-read int|null $posts_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tag whereUsageCount($value)
 */
	class Tag extends \Eloquent {}
}

namespace App\Models\Gamification{
/**
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property string|null $icon_url
 * @property string|null $tier
 * @property string|null $condition_type
 * @property int|null $condition_value
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\Gamification\BadgeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereConditionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereConditionValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereIconUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Badge whereTier($value)
 */
	class Badge extends \Eloquent {}
}

namespace App\Models\Gamification{
/**
 * @property string $id
 * @property string $user_id
 * @property int $points
 * @property string $action_type
 * @property string|null $reference_id
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Auth\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PointsLog whereUserId($value)
 */
	class PointsLog extends \Eloquent {}
}

namespace App\Models\Gamification{
/**
 * @property string $id
 * @property string $user_id
 * @property string $badge_id
 * @property \Illuminate\Support\Carbon $earned_at
 * @property-read \App\Models\Gamification\Badge $badge
 * @property-read \App\Models\Auth\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereBadgeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereEarnedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserBadge whereUserId($value)
 */
	class UserBadge extends \Eloquent {}
}

namespace App\Models\History{
/**
 * @property string $id
 * @property string $comment_id
 * @property string $edited_by
 * @property string|null $body_before
 * @property string|null $body_after
 * @property \Illuminate\Support\Carbon $edited_at
 * @property-read \App\Models\Content\Comment|null $comment
 * @property-read \App\Models\Auth\User $editor
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory whereBodyAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory whereBodyBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory whereCommentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory whereEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory whereEditedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommentEditHistory whereId($value)
 */
	class CommentEditHistory extends \Eloquent {}
}

namespace App\Models\History{
/**
 * @property string $id
 * @property string $post_id
 * @property string $edited_by
 * @property string|null $body_before
 * @property string|null $body_after
 * @property string|null $reason
 * @property \Illuminate\Support\Carbon $edited_at
 * @property-read \App\Models\Auth\User $editor
 * @property-read \App\Models\Content\Post|null $post
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory whereBodyAfter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory whereBodyBefore($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory whereEditedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory whereEditedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PostEditHistory whereReason($value)
 */
	class PostEditHistory extends \Eloquent {}
}

namespace App\Models\Interaction{
/**
 * @property string $id
 * @property string $user_id
 * @property string $post_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Content\Post|null $post
 * @property-read \App\Models\Auth\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark wherePostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bookmark whereUserId($value)
 */
	class Bookmark extends \Eloquent {}
}

namespace App\Models\Interaction{
/**
 * @property string $id
 * @property string $follower_id
 * @property string $following_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Auth\User $follower
 * @property-read \App\Models\Auth\User $following
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereFollowingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Follow whereId($value)
 */
	class Follow extends \Eloquent {}
}

namespace App\Models\Interaction{
/**
 * @property string $id
 * @property string $user_id
 * @property string $target_id
 * @property string $target_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $target
 * @property-read \App\Models\Auth\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Like whereUserId($value)
 */
	class Like extends \Eloquent {}
}

namespace App\Models\Interaction{
/**
 * @property string $id
 * @property string $user_id
 * @property string $target_id
 * @property string $target_type
 * @property string $vote_type
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $target
 * @property-read \App\Models\Auth\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereTargetType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Vote whereVoteType($value)
 */
	class Vote extends \Eloquent {}
}

namespace App\Models\Moderation{
/**
 * @property string $id
 * @property string $moderator_id
 * @property string|null $target_user_id
 * @property string $action_type
 * @property string $reason
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Auth\User $moderator
 * @property-read \App\Models\Auth\User|null $targetUser
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog whereActionType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog whereModeratorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ModerationLog whereTargetUserId($value)
 */
	class ModerationLog extends \Eloquent {}
}

namespace App\Models\Moderation{
/**
 * @property string $id
 * @property string $user_id
 * @property string|null $actor_id
 * @property string $type
 * @property string|null $reference_id
 * @property string|null $reference_type
 * @property bool $is_read
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read \App\Models\Auth\User|null $actor
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $reference
 * @property-read \App\Models\Auth\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereIsRead($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReferenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereReferenceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification whereUserId($value)
 */
	class Notification extends \Eloquent {}
}

namespace App\Models\Moderation{
/**
 * @property string $id
 * @property string $reporter_id
 * @property string $target_id
 * @property string $target_type
 * @property string $reason
 * @property string|null $description
 * @property string $status
 * @property string|null $resolved_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property-read \App\Models\Auth\User $reporter
 * @property-read \App\Models\Auth\User|null $resolver
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $target
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereReporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereResolvedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Report whereTargetType($value)
 */
	class Report extends \Eloquent {}
}

