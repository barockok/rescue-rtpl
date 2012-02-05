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

# OBJECT OWN SERVER
set :server_group, 'www-data'
set :server_user , 'nginxer'

namespace :update do
  
 
  
  task :default do
    working
    production
    pushing
    to_server
    back_to_work
  end
  
  task :set_commit_msg do
    set(:commit_msg)  do
       Capistrano::CLI.ui.ask "commit message for working branch : "
     end
  end
  
  task :working do  
    set_commit_msg
    system('git checkout working && git add .')
    system("git commit -am '#{commit_msg}' ");
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
  
  # Server side execute ##
  
  task :to_server do
      deploy.prepare_permission
      
      command = "
      cd #{applicationdir} && 
      git checkout #{branch} && 
      git reset --hard HEAD~1 &&  
      git pull origin #{branch}"
      run command
      
      deploy.repare_permission
  end
  
 
  # end server side execution ##
end

namespace :deploy  do
  desc "Search Remote Application Server Libraries"
  task :default do
    prepare_permission
    sudo "rm -rf #{applicationdir}"
    command = "
            mkdir #{applicationdir} &&
            cd #{applicationdir} &&
            git clone #{repository} #{applicationdir} &&
            git pull --all &&
            git checkout #{branch} &&
            chmod  0777 #{applicationdir}components/service/third_party/comp_maskapai/cookies/
          "
    my_run(command)
    repare_permission
  end
  task :prepare_permission do
    command = "
    chown -R #{user}:#{user} #{applicationdir}
    "
    sudo command
  end
  task :repare_permission do
     command = "
     chown -R #{server_user}:#{server_group} #{applicationdir}
     "
     sudo command
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


