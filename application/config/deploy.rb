set :user, 'deployer'
set :password, 'alzid4ever'



def red(str)
  "\e[31m#{str}\e[0m"
end

# Figure out the name of the current local branch
def current_git_branch
  branch = `git symbolic-ref HEAD 2> /dev/null`.strip.gsub(/^refs\/heads\//, '')
  puts "On branch #{red branch}"
  branch
end

# Set the deploy branch to the current branch
set :current_branch , current_git_branch
set :branch, "production"
set :domain, 'rumahtiket.com'
set :applicationdir, "/var/www/rumahtiket.com/public/platform/"
set :deploy_path , "#{applicationdir}#{branch}"
set :repository,  "git@gitlab.barockprojects.com:rt-platform"
set :use_sudo, false
role :app, "#{domain}"
role :web, "#{domain}"
role :db,  "#{domain}", :primary => true
default_run_options[:pty] = true
namespace :update do
  task :default do
    working
    production
    pushing
    to_server
    back_to_work
  
  end
  
  task :working do
  if :curent_branch != 'working' then system('git checkout working') end
    system('git add .')
    msg  = Capistrano::CLI.ui.ask "commit message for working branch : "
    system('git commit -am '+msg);
  end
  task :production do
    system('git checkout production && git merge working')
  end
  task :back_to_work do
    system('git checkout working')
  end
  task :pushing do
    system('git push origin')
  end
  task :to_server do
      command = "cd #{applicationdir} && git checkout #{branch} && git pull origin #{branch}"
      my_run(command)
  end
end

namespace :deploy  do
  desc "Search Remote Application Server Libraries"
  task :default do
    sudo "rm -rf #{applicationdir}"
    command = " 
            rm -rf #{applicationdir} &&
            mkdir #{applicationdir} &&
            cd #{applicationdir} &&
            git clone #{repository} #{applicationdir} &&
            git pull --all &&
            git checkout #{branch}
          "
    my_run(command)
  end
end

def my_run ( command ) 
  out = ''
  run command  do |channel, stream, data|
     out << data
  end
 puts "============================== "+red('OUTPUT')+" ==================================\n\n"
 puts out
 puts "\n========================================================================\n\n"
end


