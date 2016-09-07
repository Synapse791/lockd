<?php

namespace Lockd\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lockd\Contracts\Repositories\GroupRepository;
use Lockd\Services\PermissionManager;

/**
 * Class HasGroup
 *
 * @package Lockd\Http\Middleware
 * @author Iain Earl <synapse791@gmail.com>
 */
class HasGroup
{
    /** @var GroupRepository */
    private $groupRepository;

    /** @var PermissionManager */
    private $permissionChecker;

    /**
     * HasGroup constructor
     *
     * @param GroupRepository $groupRepository
     * @param PermissionManager $permissionManager
     */
    public function __construct(GroupRepository $groupRepository, PermissionManager $permissionManager)
    {
        $this->groupRepository = $groupRepository;
        $this->permissionChecker = $permissionManager;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $groupName
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next, $groupName)
    {
        $group = $this->groupRepository->findOneByName($groupName);

        if (!$group)
            throw new \Exception('Invalid group name passed');

        if (!$this->permissionChecker->checkUserIsInGroup($request->user(), $group))
            if ($request->ajax() || $request->wantsJson())
                return new JsonResponse([
                    'data' => [],
                    'error' => 'unauthorized',
                    'errorDescription' => "You must be a member of the {$groupName} group to access that",
                ]);
            else
                return redirect()->back()->withErrors(["You must be a member of the {$groupName} group to access that"]);

        return $next($request);
    }
}
